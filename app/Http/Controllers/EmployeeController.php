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
        $query = $scope->apply($query, auth()->user(), Employee::class);

        $employees = $query->latest()->get();

        return Inertia::render('HR/Employees/Index', [
            'employees' => $employees
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive,terminated',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        
        Employee::create($validated);

        return redirect()->back()->with('success', 'Employee added successfully');
    }

    public function update(Request $request, Employee $employee)
    {
        // Simple authorization check for tenant
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'leaving_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,terminated',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->back()->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        
        $employee->delete();

        return redirect()->back()->with('success', 'Employee deleted successfully');
    }
}
