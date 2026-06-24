<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Inventory;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today();

        // Today's Sales
        $todaySales = Sale::where('company_id', $companyId)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        // Today's Purchases
        $todayPurchases = Purchase::where('company_id', $companyId)
            ->whereDate('purchase_date', $today)
            ->sum('total_amount');

        // Today's Collections (Payments from Customers)
        $todayCollections = Payment::where('company_id', $companyId)
            ->where('type', 'customer_receipt')
            ->whereDate('payment_date', $today)
            ->sum('amount');

        // POS Payments (Cash/Card received during sale today)
        $todayPosCollections = Sale::where('company_id', $companyId)
            ->whereDate('sale_date', $today)
            ->sum('paid_amount');
            
        $totalCollectionsToday = $todayCollections + $todayPosCollections;

        // Current Stock Value (Qty * Buy Price)
        // We join medicines to get the buy_price
        $stockValue = Inventory::where('inventories.company_id', $companyId)
            ->join('medicines', 'inventories.medicine_id', '=', 'medicines.id')
            ->sum(DB::raw('inventories.quantity * medicines.buy_price'));

        // Recent Sales
        $recentSales = Sale::where('company_id', $companyId)
            ->with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Procurement Stats
        $pendingRequisitions = \App\Models\Requisition::where('company_id', $companyId)
            ->whereNotIn('status', ['PO Generated', 'Cancelled'])
            ->count();

        $urgentRequisitions = \App\Models\Requisition::where('company_id', $companyId)
            ->where('priority', 'Urgent')
            ->whereNotIn('status', ['PO Generated', 'Cancelled'])
            ->count();

        $pendingPos = \App\Models\PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['Pending', 'Partial'])
            ->count();

        // Items below reorder level
        $itemsBelowReorder = 0;
        $itemsWithReorder = \App\Models\Item::where('company_id', $companyId)
            ->where('inventory_type', 'Stock Item')
            ->where('is_active', true)
            ->where('reorder_level', '>', 0)
            ->get();

        foreach ($itemsWithReorder as $item) {
            $stock = Inventory::where('company_id', $companyId)->where('medicine_id', $item->id)->sum('quantity');
            if ($stock <= $item->reorder_level) {
                $itemsBelowReorder++;
            }
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'today_sales' => $todaySales,
                'today_purchases' => $todayPurchases,
                'today_collections' => $totalCollectionsToday,
                'stock_value' => $stockValue,
            ],
            'procurement' => [
                'pending_requisitions' => $pendingRequisitions,
                'urgent_requisitions' => $urgentRequisitions,
                'pending_pos' => $pendingPos,
                'items_below_reorder' => $itemsBelowReorder,
            ],
            'recent_sales' => $recentSales,
        ]);
    }
}
