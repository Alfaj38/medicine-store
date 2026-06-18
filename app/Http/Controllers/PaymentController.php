<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function pendingCreditNotes($supplierId)
    {
        $creditNotes = \App\Models\SupplierCreditNote::where('supplier_id', $supplierId)
            ->where('status', 'pending')
            ->get();
            
        return response()->json($creditNotes);
    }

    public function storeSupplierPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'credit_note_ids' => 'nullable|array',
            'credit_note_ids.*' => 'exists:supplier_credit_notes,id',
        ]);

        $dueAmount = $purchase->total_amount - $purchase->paid_amount;
        
        // Calculate total credit notes amount
        $totalCreditNoteAmount = 0;
        $creditNotes = [];
        if (!empty($validated['credit_note_ids'])) {
            $creditNotes = \App\Models\SupplierCreditNote::whereIn('id', $validated['credit_note_ids'])
                ->where('supplier_id', $purchase->supplier_id)
                ->where('status', 'pending')
                ->get();
            $totalCreditNoteAmount = $creditNotes->sum('amount');
        }

        $totalEffectivePayment = $validated['amount'] + $totalCreditNoteAmount;

        if ($totalEffectivePayment <= 0) {
            return back()->withErrors(['amount' => 'Please provide a payment amount or select a credit note.']);
        }

        if ($totalEffectivePayment > $dueAmount) {
            return back()->withErrors(['amount' => 'Total payment (cash + credit notes) cannot exceed the remaining balance of the purchase.']);
        }

        DB::transaction(function () use ($validated, $purchase, $totalEffectivePayment, $creditNotes, $totalCreditNoteAmount) {
            
            // Record the cash payment if there is any
            if ($validated['amount'] > 0) {
                Payment::create([
                    'company_id' => auth()->user()->company_id ?? 1,
                    'branch_id' => auth()->user()->branch_id ?? 1,
                    'type' => 'supplier_payment',
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $validated['transaction_id'],
                    'notes' => $validated['notes'],
                    'user_id' => auth()->id(),
                ]);
            }

            // Process Credit Notes
            foreach ($creditNotes as $cn) {
                // Record the credit note as a payment
                Payment::create([
                    'company_id' => auth()->user()->company_id ?? 1,
                    'branch_id' => auth()->user()->branch_id ?? 1,
                    'type' => 'supplier_payment',
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'payment_date' => $validated['payment_date'],
                    'amount' => $cn->amount,
                    'payment_method' => 'Credit Note',
                    'transaction_id' => $cn->credit_note_no,
                    'notes' => 'Applied Credit Note: ' . $cn->credit_note_no,
                    'user_id' => auth()->id(),
                ]);

                // Update CN status
                $cn->status = 'applied';
                $cn->save();
            }

            // Update Purchase paid_amount and payment_status
            $purchase->paid_amount += $totalEffectivePayment;
            
            if ($purchase->paid_amount >= $purchase->total_amount) {
                $purchase->payment_status = 'paid';
            } elseif ($purchase->paid_amount > 0) {
                $purchase->payment_status = 'partial';
            } else {
                $purchase->payment_status = 'unpaid';
            }
            
            $purchase->save();

            // Update Supplier's current_balance (decrease balance since we paid them or applied credit)
            $supplier = Supplier::find($purchase->supplier_id);
            if ($supplier) {
                // Note: Credit notes technically already reduced the supplier balance when they were created (in Phase 1)?
                // Let me check Phase 1 code: We did NOT reduce supplier current_balance in the PurchaseReturnController!
                // Wait, if PurchaseReturnController didn't reduce it, then doing it here is fine.
                // Cash payment reduces supplier balance.
                // Credit Note application reduces supplier balance.
                $supplier->current_balance -= $totalEffectivePayment;
                $supplier->save();
            }
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
