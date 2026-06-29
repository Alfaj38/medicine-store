<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');
        
        $type = $request->get('type'); // 'income' or 'expense'
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $query = Transaction::with('account')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($type) {
            $query->where('type', $type);
        }

        $query = $scope->apply($query, auth()->user(), Transaction::class);
        $transactions = $query->latest('date')->latest('id')->get();

        // Get accounts for the dropdown
        $accountQuery = Account::where('status', 'active');
        $accountQuery = $scope->apply($accountQuery, auth()->user(), Account::class);
        $accounts = $accountQuery->get();

        // Calculate summary
        $summary = [
            'total_income' => $transactions->where('type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];
        $summary['net_profit'] = $summary['total_income'] - $summary['total_expense'];

        return Inertia::render('Accounting/Transactions/Index', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'summary' => $summary,
            'filters' => [
                'type' => $type,
                'month' => (int)$month,
                'year' => (int)$year,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated['company_id'] = auth()->user()->company_id;
        
        Transaction::create($validated);

        return redirect()->back()->with('success', 'Transaction recorded successfully');
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        if ($account->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $transaction->update($validated);

        return redirect()->back()->with('success', 'Transaction updated successfully');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully');
    }
}
