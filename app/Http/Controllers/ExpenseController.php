<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $scope = app('data_scope');

        $query = Expense::query();
        $query = $scope->apply($query, auth()->user(), Expense::class)
            ->with(['category', 'user']);

        $expenses = $query->latest()->paginate(15);

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses
        ]);
    }
}
