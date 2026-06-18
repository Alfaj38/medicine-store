<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventory;
use App\Models\Medicine;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function expiry()
    {
        $scope = app('data_scope');

        $query = Inventory::query();
        $query = $scope->apply($query, auth()->user(), Inventory::class);

        // Get all inventory items with expiry dates, sorted by nearest expiry
        $expiringInventory = (clone $query)->with('medicine')
            ->whereNotNull('expiry_date')
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->paginate(20);

        $now = Carbon::now();
        $threeMonthsFromNow = Carbon::now()->addMonths(3);
        $sixMonthsFromNow = Carbon::now()->addMonths(6);

        // Calculate summary stats
        $stats = [
            'already_expired' => (clone $query)
                ->where('quantity', '>', 0)
                ->where('expiry_date', '<', $now)
                ->count(),
            'expiring_in_3_months' => (clone $query)
                ->where('quantity', '>', 0)
                ->whereBetween('expiry_date', [$now, $threeMonthsFromNow])
                ->count(),
            'expiring_in_6_months' => (clone $query)
                ->where('quantity', '>', 0)
                ->whereBetween('expiry_date', [$now, $sixMonthsFromNow])
                ->count(),
        ];

        return Inertia::render('Reports/Expiry', [
            'inventory' => $expiringInventory,
            'stats' => $stats,
            'now' => $now->toDateString(),
        ]);
    }

    public function lowStock()
    {
        $scope = app('data_scope');

        // We apply data scope on Inventory inside the sum relationship
        $lowStockMedicines = Medicine::where('is_active', true)
            ->withSum(['inventory as total_stock' => function($q) use ($scope) {
                $scope->apply($q, auth()->user(), Inventory::class);
            }], 'quantity')
            ->having('total_stock', '<=', \DB::raw('reorder_level'))
            ->orHavingNull('total_stock')
            ->orderBy('total_stock')
            ->paginate(20);

        return Inertia::render('Reports/LowStock', [
            'medicines' => $lowStockMedicines,
        ]);
    }
}
