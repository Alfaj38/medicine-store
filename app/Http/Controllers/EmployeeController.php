<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $scope = app('data_scope');

        $query = Employee::query();
        $query = $scope->apply($query, auth()->user(), Employee::class)
            ->with(['branch', 'user']);

        $employees = $query->latest()->paginate(15);

        return Inertia::render('Employees/Index', [
            'employees' => $employees
        ]);
    }
}
