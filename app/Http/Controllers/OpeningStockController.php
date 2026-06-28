<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Medicine;
use App\Models\Inventory;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;

class OpeningStockController extends Controller
{
    public function index()
    {
        return Inertia::render('Inventory/OpeningStock');
    }

    public function search(Request $request)
    {
        $queryText = $request->get('q');
        
        if (!$queryText) {
            return response()->json([]);
        }

        $items = Medicine::where('is_active', true)
            ->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('barcode', 'like', "%{$queryText}%")
                  ->orWhere('generic_name', 'like', "%{$queryText}%");
            })
            ->with('unit:id,name')
            ->select(['id', 'name', 'generic_name', 'mrp', 'unit_id'])
            ->take(15)
            ->get()->map(function($item) {
                return [
                    'id'           => $item->id,
                    'name'         => $item->name,
                    'generic_name' => $item->generic_name,
                    'uom'          => $item->unit ? $item->unit->name : 'Unit',
                    'mrp'          => $item->mrp ?: 0,
                    'tp'           => $item->mrp ? round($item->mrp * 0.88, 2) : 0,
                ];
            });

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:medicines,id',
            'items.*.batch_no' => 'required|string',
            'items.*.expiry_date' => 'required|date',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.mrp' => 'required|numeric|min:0',
            'items.*.tp' => 'required|numeric|min:0',
        ]);

        $companyId = auth()->user()->company_id;

        DB::transaction(function () use ($validated, $companyId) {
            foreach ($validated['items'] as $item) {
                // Check if identical inventory batch already exists
                $inventory = Inventory::where('company_id', $companyId)
                    ->where('medicine_id', $item['id'])
                    ->where('batch_no', $item['batch_no'])
                    ->where('expiry_date', $item['expiry_date'])
                    ->first();

                $oldBalance = 0;
                
                if ($inventory) {
                    $oldBalance = $inventory->quantity;
                    $inventory->quantity += $item['quantity'];
                    // Update prices if needed, or stick to the ones provided
                    $inventory->mrp = $item['mrp'];
                    $inventory->trade_price = $item['tp'];
                    $inventory->save();
                } else {
                    $inventory = Inventory::create([
                        'company_id' => $companyId,
                        'medicine_id' => $item['id'],
                        'batch_no' => $item['batch_no'],
                        'expiry_date' => $item['expiry_date'],
                        'quantity' => $item['quantity'],
                        'mrp' => $item['mrp'],
                        'trade_price' => $item['tp'],
                    ]);
                }

                // Log the ledger
                StockLedger::create([
                    'company_id' => $companyId,
                    'medicine_id' => $item['id'],
                    'batch_no' => $item['batch_no'],
                    'reference_type' => 'OpeningStock',
                    'reference_id' => $inventory->id,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'balance_after' => $oldBalance + $item['quantity'],
                    'notes' => 'Opening stock entry'
                ]);
            }
        });

        return redirect()->back()->with('success', 'Opening stock saved successfully.');
    }
}
