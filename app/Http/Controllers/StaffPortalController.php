<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class StaffPortalController extends Controller
{
    public function dashboard()
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->first();

        if (!$employee) {
            return redirect()->route('login')->withErrors(['email' => 'No staff profile associated with this account.']);
        }

        $attendances = EmployeeAttendance::where('employee_id', $employee->id)->latest()->limit(30)->get();
        
        $presentDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $lateDays = $attendances->where('status', 'Late')->count();
        $totalDays = $attendances->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        // Fetch assigned tasks sorted by priority & status
        $assignedTasks = \App\Models\Task::where('assigned_to', $employee->id)
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
            ->orderBy('due_date', 'asc')
            ->get();

        // Fetch today's logged work update
        $todayUpdate = \App\Models\DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();
            
        $salarySlips = \App\Models\SalarySlip::where('employee_id', $employee->id)->latest('created_at')->get();

        return view('portal.staff.dashboard', compact(
            'employee', 'attendances', 'presentDays', 'absentDays', 'lateDays', 'attendancePercentage', 'assignedTasks', 'todayUpdate', 'salarySlips'
        ));
    }
}
