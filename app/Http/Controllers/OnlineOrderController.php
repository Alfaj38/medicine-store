<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OnlineOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = OnlineOrder::with('items')
            ->where('company_id', auth()->user()->company_id)
            ->when($request->search, function ($query, $search) {
                $query->where('tracking_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                if ($status !== 'all') {
                    $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('OnlineOrders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(OnlineOrder $onlineOrder)
    {
        if ($onlineOrder->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $onlineOrder->load('items');

        return Inertia::render('OnlineOrders/Show', [
            'order' => $onlineOrder,
        ]);
    }

    public function update(Request $request, OnlineOrder $onlineOrder)
    {
        if ($onlineOrder->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,processing,out_for_delivery,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed',
        ]);

        $onlineOrder->update($validated);

        return back()->with('success', 'Order status updated successfully.');
    }
}
