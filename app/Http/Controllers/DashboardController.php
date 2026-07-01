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

        // Phase 11: Dashboard Optimization - Cache KPIs for 5 minutes
        $cacheKey = "company_{$companyId}_dashboard_data";
        
        $dashboardData = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(5), function () use ($companyId, $today, $startOfWeek, $endOfWeek) {
            // 1. TOP CARDS (Stats)
            $todaySales = Sale::where('company_id', $companyId)->whereDate('sale_date', $today)->sum('total_amount');
            $todayPurchases = Purchase::where('company_id', $companyId)->whereDate('purchase_date', $today)->sum('total_amount');
            $todayCollections = Payment::where('company_id', $companyId)->where('type', 'customer_receipt')->whereDate('payment_date', $today)->sum('amount');
            $todayPosCollections = Sale::where('company_id', $companyId)->whereDate('sale_date', $today)->sum('paid_amount');
            $totalCollectionsToday = $todayCollections + $todayPosCollections;
            $stockValue = Inventory::where('company_id', $companyId)
                ->sum(DB::raw('quantity * trade_price'));

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
                ->leftJoin('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
                ->leftJoin('medicine_categories', 'medicines.category_id', '=', 'medicine_categories.id')
                ->leftJoin('items', 'sale_items.medicine_id', '=', 'items.id')
                ->leftJoin('item_categories', 'items.category_id', '=', 'item_categories.id')
                ->where('sales.company_id', $companyId)
                ->whereBetween('sales.sale_date', [$startOfWeek, $endOfWeek])
                ->select(
                    DB::raw('COALESCE(medicine_categories.name, item_categories.name, "Uncategorized") as category_name'), 
                    DB::raw('SUM(sale_items.total) as total_sales')
                )
                ->groupBy('category_name')
                ->orderBy('total_sales', 'desc')
                ->get();

            // 4. TOP SELLING PRODUCTS (Current Week)
            $topProducts = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoin('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
                ->leftJoin('items', 'sale_items.medicine_id', '=', 'items.id')
                ->where('sales.company_id', $companyId)
                ->whereBetween('sales.sale_date', [$startOfWeek, $endOfWeek])
                ->select(
                    DB::raw('COALESCE(medicines.name, items.name, "Unknown") as name'), 
                    DB::raw('SUM(sale_items.quantity) as qty_sold'), 
                    DB::raw('SUM(sale_items.total) as revenue')
                )
                ->groupBy('sale_items.medicine_id', 'name')
                ->orderBy('qty_sold', 'desc')
                ->take(5)
                ->get();

            // 5. LOW STOCK ALERTS
            // Optimized to not load huge relationships if there are 50k inventories
            $inventories = DB::table('inventories')
                ->where('company_id', $companyId)
                ->select('medicine_id', DB::raw('SUM(quantity) as stock'))
                ->groupBy('medicine_id')
                ->get();
                
            $groupedStock = [];
            foreach ($inventories as $inv) {
                $medId = $inv->medicine_id;
                $groupedStock[$medId] = [
                    'id' => $medId,
                    'name' => 'Medicine #' . $medId,
                    'stock' => $inv->stock,
                    'reorder_level' => 10
                ];
            }

            $lowStockItems = [];
            $itemsBelowReorderCount = 0;
            foreach ($groupedStock as $item) {
                if ($item['stock'] <= $item['reorder_level']) {
                    $itemsBelowReorderCount++;
                    $status = $item['stock'] <= ($item['reorder_level'] / 2) ? 'Critical' : 'Low Stock';
                    $lowStockItems[] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'stock' => $item['stock'],
                        'status' => $status,
                    ];
                }
            }
            usort($lowStockItems, fn($a, $b) => $a['stock'] <=> $b['stock']);
            $lowStockItems = array_slice($lowStockItems, 0, 5);

            // Fetch actual names for the top 5 low stock items
            $medIds = array_column($lowStockItems, 'id');
            if(count($medIds) > 0) {
                $meds = \App\Models\Medicine::whereIn('id', $medIds)->pluck('name', 'id');
                $items = \App\Models\Item::whereIn('id', $medIds)->pluck('name', 'id');
                foreach($lowStockItems as &$ls) {
                    $ls['name'] = $meds[$ls['id']] ?? $items[$ls['id']] ?? 'Unknown';
                }
            }

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

            return [
                'today_sales' => $todaySales,
                'today_purchases' => $todayPurchases,
                'today_collections' => $totalCollectionsToday,
                'stock_value' => $stockValue,
                'sales_overview' => $salesOverview,
                'sales_by_category' => $salesByCategory,
                'top_products' => $topProducts,
                'low_stock_items' => $lowStockItems,
                'items_below_reorder' => $itemsBelowReorderCount,
                'payment_methods' => $paymentMethods,
                'pending_requisitions' => $pendingRequisitions,
                'urgent_requisitions' => $urgentRequisitions,
                'pending_pos' => $pendingPos,
            ];
        });
        
        $recentSales = Sale::where('company_id', $companyId)->with(['customer', 'user'])->orderBy('created_at', 'desc')->take(5)->get();

        return Inertia::render('Dashboard', [
            'stats' => Inertia::defer(fn () => [
                'today_sales' => $dashboardData['today_sales'],
                'today_purchases' => $dashboardData['today_purchases'],
                'today_collections' => $dashboardData['today_collections'],
                'stock_value' => $dashboardData['stock_value'],
            ]),
            'procurement' => Inertia::defer(fn () => [
                'pending_requisitions' => $dashboardData['pending_requisitions'],
                'urgent_requisitions' => $dashboardData['urgent_requisitions'],
                'pending_pos' => $dashboardData['pending_pos'],
                'items_below_reorder' => $dashboardData['items_below_reorder'],
            ]),
            'recent_sales' => Inertia::defer(fn () => $recentSales),
            'charts' => Inertia::defer(fn () => [
                'sales_overview' => $dashboardData['sales_overview'],
                'sales_by_category' => $dashboardData['sales_by_category'],
                'top_products' => $dashboardData['top_products'],
                'low_stock' => $dashboardData['low_stock_items'],
                'payment_methods' => $dashboardData['payment_methods'],
            ])
        ]);
    }
}
