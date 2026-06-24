<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\StockLedger;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaleReturnController extends Controller
{
    public function create(Request $request)
    {
        $saleId = $request->get('sale_id');
        $sale = Sale::with(['customer', 'items.medicine'])->findOrFail($saleId);

        // Ensure the sale belongs to the company
        if ($sale->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        return Inertia::render('Sales/Return', [
            'sale' => $sale
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.return_quantity' => 'required|integer|min:1',
        ]);

        $sale = Sale::findOrFail($validated['sale_id']);

        if ($sale->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return DB::transaction(function () use ($validated, $sale) {
            $totalRefundAmount = 0;
            $returnNo = 'SR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $saleReturn = SaleReturn::create([
                'company_id' => auth()->user()->company_id,
                'branch_id' => $sale->branch_id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_no' => $returnNo,
                'return_date' => now()->toDateString(),
                'total_amount' => 0, // Will update later
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $saleItem = SaleItem::findOrFail($itemData['sale_item_id']);
                
                $availableToReturn = $saleItem->quantity - $saleItem->returned_quantity;
                if ($itemData['return_quantity'] > $availableToReturn) {
                    throw new \Exception("Cannot return more than sold quantity for " . ($saleItem->medicine->name ?? 'Unknown Item'));
                }

                $subtotal = $itemData['return_quantity'] * $saleItem->unit_price;
                $totalRefundAmount += $subtotal;

                // Create Return Item
                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'medicine_id' => $saleItem->medicine_id,
                    'batch_no' => $saleItem->batch_no,
                    'quantity' => $itemData['return_quantity'],
                    'unit_price' => $saleItem->unit_price,
                    'subtotal' => $subtotal,
                ]);

                // Update Sale Item
                $saleItem->increment('returned_quantity', $itemData['return_quantity']);

                // Restock Inventory
                $inventory = Inventory::firstOrCreate(
                    [
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => $sale->branch_id,
                        'medicine_id' => $saleItem->medicine_id,
                        'batch_no' => $saleItem->batch_no,
                    ],
                    [
                        'quantity' => 0,
                    ]
                );

                $inventory->increment('quantity', $itemData['return_quantity']);

                // Ledger Entry
                StockLedger::create([
                    'company_id' => auth()->user()->company_id,
                    'branch_id' => $sale->branch_id,
                    'medicine_id' => $saleItem->medicine_id,
                    'batch_no' => $saleItem->batch_no,
                    'transaction_type' => 'sale_return',
                    'transaction_id' => $saleReturn->id,
                    'quantity_in' => $itemData['return_quantity'],
                    'quantity_out' => 0,
                    'balance' => $inventory->quantity,
                    'notes' => 'Sales return for invoice: ' . $sale->invoice_no,
                ]);
            }

            $saleReturn->update(['total_amount' => $totalRefundAmount]);

            // Adjust Customer Balance (If registered customer)
            // A refund means they owe us less (or we owe them). We deduct the amount from their current_balance.
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                $customer->current_balance -= $totalRefundAmount;
                $customer->save();
            }

            return redirect()->route('sales.index')->with('success', 'Return processed and inventory restocked successfully.');
        });
    }
}
