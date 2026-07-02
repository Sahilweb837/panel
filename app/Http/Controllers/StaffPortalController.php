<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\Employee;
use App\Models\StaffOfferLetter;
use App\Models\LeaveApplication;
use App\Models\StaffIncomeRecord;
use App\Models\SalarySlip;
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

        $assignedTasks = \App\Models\Task::where('assigned_to', $employee->id)
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
            ->orderBy('due_date', 'asc')
            ->get();

        $todayUpdate = \App\Models\DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $salarySlips = SalarySlip::where('employee_id', $employee->id)->latest('created_at')->get();

        $offerLetters = StaffOfferLetter::where('employee_id', $employee->id)->latest()->get();
        $leaveApplications = LeaveApplication::where('employee_id', $employee->id)->latest()->get();
        $incomeRecords = StaffIncomeRecord::where('employee_id', $employee->id)->latest()->get();

        $totalIncome = $incomeRecords->where('status', 'Received')->sum('amount');

        return view('portal.staff.dashboard', compact(
            'employee', 'attendances', 'presentDays', 'absentDays', 'lateDays', 'attendancePercentage', 
            'assignedTasks', 'todayUpdate', 'salarySlips', 'offerLetters', 'leaveApplications', 
            'incomeRecords', 'totalIncome'
        ));
    }

    public function offerLetters()
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $offerLetters = StaffOfferLetter::where('employee_id', $employee->id)->latest()->get();
        return view('portal.staff.offer-letters', compact('employee', 'offerLetters'));
    }

    public function leaveApplications()
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $leaves = LeaveApplication::where('employee_id', $employee->id)->latest()->get();
        return view('portal.staff.leave', compact('employee', 'leaves'));
    }

    public function incomeRecords()
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $incomeRecords = StaffIncomeRecord::where('employee_id', $employee->id)->latest()->get();
        $totalIncome = $incomeRecords->where('status', 'Received')->sum('amount');
        return view('portal.staff.income', compact('employee', 'incomeRecords', 'totalIncome'));
    }
}

        $attendances = EmployeeAttendance::where('employee_id', $employee->id)->latest()->limit(30)->get();
        
        $presentDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $lateDays = $attendances->where('status', 'Late')->count();
        $totalDays = $attendances->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        $assignedTasks = \App\Models\Task::where('assigned_to', $employee->id)
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
            ->orderBy('due_date', 'asc')
            ->get();

        $todayUpdate = \App\Models\DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $salarySlips = SalarySlip::where('employee_id', $employee->id)->latest('created_at')->get();

        $offerLetters = StaffOfferLetter::where('employee_id', $employee->id)->latest()->get();
        $leaveApplications = LeaveApplication::where('employee_id', $employee->id)->latest()->get();
        $incomeRecords = StaffIncomeRecord::where('employee_id', $employee->id)->latest()->get();

        $totalIncome = $incomeRecords->where('status', 'Received')->sum('amount');

        return view('portal.staff.dashboard', compact(
            'employee', 'attendances', 'presentDays', 'absentDays', 'lateDays', 'attendancePercentage', 
            'assignedTasks', 'todayUpdate', 'salarySlips', 'offerLetters', 'leaveApplications', 
            'incomeRecords', 'totalIncome'
        ));
    }
}
