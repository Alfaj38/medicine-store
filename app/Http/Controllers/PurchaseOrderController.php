<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $purchaseOrders = PurchaseOrder::where('company_id', auth()->user()->company_id)
            ->with(['supplier', 'requisition'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $purchaseOrder->load(['items.item', 'supplier', 'requisition']);

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $purchaseOrder
        ]);
    }
}
