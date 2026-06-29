<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');
        
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $query = Employee::query();
        $query = $scope->apply($query, auth()->user(), Employee::class);
        $employees = $query->get();

        $payrollQuery = SalaryPayment::where('month', $month)->where('year', $year)->with('employee');
        $payrollQuery = $scope->apply($payrollQuery, auth()->user(), SalaryPayment::class);
        $payrolls = $payrollQuery->get();

        return Inertia::render('HR/Payroll/Index', [
            'employees' => $employees,
            'payrolls' => $payrolls,
            'currentMonth' => (int)$month,
            'currentYear' => (int)$year,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deduction' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated['company_id'] = auth()->user()->company_id;
        $validated['net_salary'] = $validated['basic_salary'] + $validated['bonus'] - $validated['deduction'];

        SalaryPayment::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Payroll processed successfully');
    }
}
