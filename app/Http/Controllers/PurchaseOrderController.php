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
    public function create()
    {
        $suppliers = \App\Models\Supplier::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->get();

        $requisitions = \App\Models\Requisition::where('company_id', auth()->user()->company_id)
            ->whereIn('status', ['Draft', 'Owner Approved', 'Accounts Approved'])
            ->with(['items.item.unit'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('PurchaseOrders/Form', [
            'suppliers' => $suppliers,
            'requisitions' => $requisitions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'requisition_id' => 'nullable|exists:requisitions,id',
            'expected_delivery_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.ordered_qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        \DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            
            $po = PurchaseOrder::create([
                'company_id' => auth()->user()->company_id,
                'supplier_id' => $validated['supplier_id'],
                'requisition_id' => $validated['requisition_id'] ?? null,
                'po_no' => 'PO-' . strtoupper(uniqid()),
                'status' => 'Pending',
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'created_by' => auth()->id()
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = $item['unit_price'] * $item['ordered_qty'];
                $totalAmount += $subtotal;
                
                $po->items()->create([
                    'item_id' => $item['item_id'],
                    'ordered_qty' => $item['ordered_qty'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal
                ]);
            }

            $po->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
    }
}
