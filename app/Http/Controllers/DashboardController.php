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
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // 1. TOP CARDS (Stats)
        $todaySales = Sale::where('company_id', $companyId)->whereDate('sale_date', $today)->sum('total_amount');
        $todayPurchases = Purchase::where('company_id', $companyId)->whereDate('purchase_date', $today)->sum('total_amount');
        $todayCollections = Payment::where('company_id', $companyId)->where('type', 'customer_receipt')->whereDate('payment_date', $today)->sum('amount');
        $todayPosCollections = Sale::where('company_id', $companyId)->whereDate('sale_date', $today)->sum('paid_amount');
        $totalCollectionsToday = $todayCollections + $todayPosCollections;
        $stockValue = Inventory::where('inventories.company_id', $companyId)
            ->join('medicines', 'inventories.medicine_id', '=', 'medicines.id')
            ->sum(DB::raw('inventories.quantity * medicines.buy_price'));

        // 2. SALES OVERVIEW (Last 7 Days)
        $salesOverview = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $amount = Sale::where('company_id', $companyId)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');
            $salesOverview[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'amount' => (float)$amount
            ];
        }

        // 3. SALES BY CATEGORY (Current Week)
        $salesByCategory = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('items', 'sale_items.medicine_id', '=', 'items.id')
            ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
            ->where('sales.company_id', $companyId)
            ->whereBetween('sales.sale_date', [$startOfWeek, $endOfWeek])
            ->select(
                DB::raw('COALESCE(item_categories.name, "Uncategorized") as category_name'), 
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->groupBy('category_name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // 4. TOP SELLING PRODUCTS (Current Week)
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('items', 'sale_items.medicine_id', '=', 'items.id')
            ->where('sales.company_id', $companyId)
            ->whereBetween('sales.sale_date', [$startOfWeek, $endOfWeek])
            ->select('items.name', DB::raw('SUM(sale_items.quantity) as qty_sold'), DB::raw('SUM(sale_items.total) as revenue'))
            ->groupBy('items.id', 'items.name')
            ->orderBy('qty_sold', 'desc')
            ->take(5)
            ->get();

        // 5. LOW STOCK ALERTS
        $itemsWithReorder = \App\Models\Item::where('company_id', $companyId)
            ->where('inventory_type', 'Stock Item')
            ->where('is_active', true)
            ->where('reorder_level', '>', 0)
            ->get();
            
        $lowStockItems = [];
        $itemsBelowReorderCount = 0;
        foreach ($itemsWithReorder as $item) {
            $stock = Inventory::where('company_id', $companyId)->where('medicine_id', $item->id)->sum('quantity');
            if ($stock <= $item->reorder_level) {
                $itemsBelowReorderCount++;
                $status = $stock <= ($item->reorder_level / 2) ? 'Critical' : 'Low Stock';
                $lowStockItems[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'stock' => $stock,
                    'status' => $status,
                ];
            }
        }
        usort($lowStockItems, fn($a, $b) => $a['stock'] <=> $b['stock']);
        $lowStockItems = array_slice($lowStockItems, 0, 5);

        // 6. PAYMENT METHODS (This Week)
        $paymentMethods = DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
            ->select('payment_method', DB::raw('SUM(paid_amount) as total_paid'))
            ->whereNotNull('payment_method')
            ->where('paid_amount', '>', 0)
            ->groupBy('payment_method')
            ->orderBy('total_paid', 'desc')
            ->get();

        // 7. PROCUREMENT STATS & RECENT SALES
        $pendingRequisitions = \App\Models\Requisition::where('company_id', $companyId)->whereNotIn('status', ['PO Generated', 'Cancelled'])->count();
        $urgentRequisitions = \App\Models\Requisition::where('company_id', $companyId)->where('priority', 'Urgent')->whereNotIn('status', ['PO Generated', 'Cancelled'])->count();
        $pendingPos = \App\Models\PurchaseOrder::where('company_id', $companyId)->whereIn('status', ['Pending', 'Partial'])->count();
        
        $recentSales = Sale::where('company_id', $companyId)->with(['customer', 'user'])->orderBy('created_at', 'desc')->take(5)->get();

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
                'items_below_reorder' => $itemsBelowReorderCount,
            ],
            'recent_sales' => $recentSales,
            'charts' => [
                'sales_overview' => $salesOverview,
                'sales_by_category' => $salesByCategory,
                'top_products' => $topProducts,
                'low_stock' => $lowStockItems,
                'payment_methods' => $paymentMethods,
            ]
        ]);
    }
}
