<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\StockLedger;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'current_balance']);
        $categories = MedicineCategory::where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug']);
        $scope = app('data_scope');
        $saleQuery = Sale::query();
        $saleQuery = $scope->apply($saleQuery, auth()->user(), Sale::class);

        $recentSales = $saleQuery->where('sale_date', '>=', now()->startOfDay())
            ->with(['customer'])
            ->latest()
            ->take(10)
            ->get(['id', 'invoice_no', 'total_amount', 'payment_status', 'customer_id', 'sale_date']);

        return Inertia::render('Pos/Index', [
            'customers' => $customers,
            'categories' => $categories,
            'recentSales' => $recentSales,
        ]);
    }

    public function browseMedicines(Request $request)
    {
        $categoryId = $request->get('category_id');
        $filter = $request->get('filter', 'all'); // all, low_stock, expiring_soon

        $query = Medicine::where('is_active', true)
            ->with(['inventory' => function($q) {
                $q->where('company_id', auth()->user()->company_id);
            }, 'category']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filter === 'low_stock') {
            $query->whereHas('inventory', function($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->where('quantity', '>', 0)
                  ->whereRaw('quantity <= reorder_level');
            });
        } elseif ($filter === 'expiring_soon') {
            $query->whereHas('inventory', function($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->where('quantity', '>', 0)
                  ->where('expiry_date', '<=', now()->addDays(90));
            });
        } else {
            $query->whereHas('inventory', function($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->where('quantity', '>', 0);
            });
        }

        $medicines = $query->take(32)->get();

        return response()->json($medicines);
    }

    public function quickCustomer(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::create([
            'company_id'      => auth()->user()->company_id,
            'name'            => $validated['name'],
            'phone'           => $validated['phone'] ?? null,
            'address'         => $validated['address'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'current_balance' => $validated['opening_balance'] ?? 0,
            'is_active'       => true,
        ]);

        return redirect()->back()->with('newCustomer', $customer->only(['id', 'name', 'phone', 'current_balance']));
    }

    public function searchMedicines(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $medicines = Medicine::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('generic_name', 'like', "%{$query}%");
            })
            ->whereHas('inventory', function($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->where('quantity', '>', 0);
            })
            ->with(['inventory' => function($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->where('quantity', '>', 0);
            }])
            ->take(15)
            ->get();

        return response()->json($medicines);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.batch_no' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $paymentStatus = 'unpaid';
            if ($validated['paid_amount'] >= $validated['total_amount']) {
                $paymentStatus = 'paid';
            } elseif ($validated['paid_amount'] > 0) {
                $paymentStatus = 'partial';
            }

            // Generate Invoice No
            $invoiceNo = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $sale = Sale::create([
                'company_id' => auth()->user()->company_id,
                'branch_id' => auth()->user()->branch_id,
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'invoice_no' => $invoiceNo,
                'sale_date' => now(),
                'subtotal' => $validated['subtotal'],
                'discount' => $validated['discount'] ?? 0,
                'tax' => $validated['tax'] ?? 0,
                'total_amount' => $validated['total_amount'],
                'paid_amount' => $validated['paid_amount'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'status' => 'completed',
            ]);

            foreach ($validated['items'] as $item) {
                $sale->items()->create([
                    'medicine_id' => $item['medicine_id'],
                    'batch_no' => $item['batch_no'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'discount' => 0,
                    'total' => $item['subtotal'],
                ]);

                // Deduct Inventory
                $inventory = Inventory::where('company_id', auth()->user()->company_id)
                    ->where('medicine_id', $item['medicine_id'])
                    ->when($item['batch_no'], function($q) use ($item) {
                        return $q->where('batch_no', $item['batch_no']);
                    })
                    ->first();

                if ($inventory) {
                    $inventory->decrement('quantity', $item['quantity']);
                    
                    // Stock Ledger
                    StockLedger::create([
                        'medicine_id' => $item['medicine_id'],
                        'batch_no' => $item['batch_no'],
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'type' => 'out',
                        'quantity' => -$item['quantity'],
                        'balance_after' => $inventory->fresh()->quantity,
                        'notes' => 'Sale ' . $invoiceNo,
                    ]);
                }
            }

            // Customer Balance
            if ($validated['customer_id']) {
                $due = $validated['total_amount'] - $validated['paid_amount'];
                if ($due != 0) { // Can be negative if overpaid (advance)
                    Customer::where('id', $validated['customer_id'])->increment('current_balance', $due);
                }
            }

            return redirect()->route('sales.show', $sale->id);
        });
    }
}
