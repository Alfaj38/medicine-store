<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Inventory;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $companyId = auth()->user()->company_id;

        // Fetch all non-zero inventory for this company, grouped by medicine
        $inventoriesQuery = Inventory::with(['medicine.unit', 'customItem.uom'])
            ->where('company_id', $companyId)
            ->where('quantity', '>', 0);
            
        if ($search) {
            $inventoriesQuery->where(function($query) use ($search) {
                $query->whereHas('medicine', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('generic_name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                })->orWhereHas('customItem', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            });
        }

        $inventories = $inventoriesQuery->get();

        // Group the inventories by medicine ID
        $grouped = [];
        foreach ($inventories as $inv) {
            $product = $inv->medicine ?? $inv->customItem;
            if (!$product) continue;
            
            $medId = $inv->medicine_id;
            $isCustom = $inv->medicine === null;
            
            if (!isset($grouped[$medId])) {
                $grouped[$medId] = [
                    'medicine_id' => $medId,
                    'medicine_name' => $product->name,
                    'generic_name' => $isCustom ? 'Custom Item' : ($product->generic_name ?? ''),
                    'unit' => $isCustom ? ($product->uom ? $product->uom->name : 'Unit') : ($product->unit ? $product->unit->name : 'Unit'),
                    'total_quantity' => 0,
                    'avg_mrp' => 0,
                    'avg_tp' => 0,
                    'total_value' => 0,
                    'batches' => []
                ];
            }
            
            $grouped[$medId]['total_quantity'] += $inv->quantity;
            $grouped[$medId]['total_value'] += ($inv->quantity * $inv->trade_price);
            
            // Add batch details
            $grouped[$medId]['batches'][] = [
                'id' => $inv->id,
                'batch_no' => $inv->batch_no,
                'expiry_date' => $inv->expiry_date ? $inv->expiry_date->format('Y-m-d') : null,
                'quantity' => $inv->quantity,
                'mrp' => $inv->mrp,
                'tp' => $inv->trade_price
            ];
        }

        // Calculate averages for top level
        foreach ($grouped as &$group) {
            if ($group['total_quantity'] > 0) {
                $group['avg_tp'] = round($group['total_value'] / $group['total_quantity'], 2);
                
                // Average MRP based on current batches
                $totalMrp = array_sum(array_column($group['batches'], 'mrp'));
                $group['avg_mrp'] = count($group['batches']) > 0 ? round($totalMrp / count($group['batches']), 2) : 0;
            }
        }

        // Convert grouped array to indexed array and sort by name
        $finalData = array_values($grouped);
        usort($finalData, function($a, $b) {
            return strcmp($a['medicine_name'], $b['medicine_name']);
        });

        // Simple manual pagination since we grouped locally
        $page = $request->input('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $paginatedData = array_slice($finalData, $offset, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            count($finalData),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Inventory/Index', [
            'inventory' => $paginator,
            'filters' => ['search' => $search],
        ]);
    }
}
