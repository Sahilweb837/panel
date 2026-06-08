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

        return view('portal.staff.dashboard', compact(
            'employee', 'attendances', 'presentDays', 'absentDays', 'lateDays', 'attendancePercentage'
        ));
    }
}
