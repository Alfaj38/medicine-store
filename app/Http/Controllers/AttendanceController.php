<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $scope = app('data_scope');
        
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $query = Employee::query();
        $query = $scope->apply($query, auth()->user(), Employee::class);
        $employees = $query->get();

        $attendanceQuery = Attendance::whereMonth('date', $month)->whereYear('date', $year);
        $attendanceQuery = $scope->apply($attendanceQuery, auth()->user(), Attendance::class);
        $attendances = $attendanceQuery->get();

        return Inertia::render('HR/Attendance/Index', [
            'employees' => $employees,
            'attendances' => $attendances,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,leave,half-day',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated['company_id'] = auth()->user()->company_id;

        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Attendance recorded successfully');
    }
}
