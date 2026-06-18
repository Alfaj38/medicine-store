<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');

        $query = Sale::query();
        $query = $scope->apply($query, auth()->user(), Sale::class)->with(['customer']);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        } else {
            // Default to today
            $query->whereDate('sale_date', '>=', now()->startOfDay());
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        $sales = $query->latest()->paginate(20)->withQueryString();

        $totals = [
            'revenue' => $query->sum('total_amount'),
            'discount' => $query->sum('discount'),
            'cash_collected' => $query->sum('paid_amount'),
        ];

        return \Inertia\Inertia::render('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only(['start_date', 'end_date']),
            'totals' => $totals,
        ]);
    }

    public function show($id)
    {
        $scope = app('data_scope');
        
        $sale = Sale::query();
        $sale = $scope->apply($sale, auth()->user(), Sale::class)
            ->with(['items.medicine', 'customer', 'user'])
            ->findOrFail($id);

        return Inertia::render('Sales/Receipt', [
            'sale' => $sale,
        ]);
    }
}
