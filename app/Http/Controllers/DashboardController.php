<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\FeeInvoice;
use App\Models\SalarySlip;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::with(['role', 'employee'])->find(session('user_id'));

        if ($user?->role?->slug === 'staff') {
            $employee = $user->employee;

            return view('staff.dashboard', [
                'employee' => $employee,
                'salarySlips' => $employee
                    ? SalarySlip::where('employee_id', $employee->id)->latest()->limit(6)->get()
                    : collect(),
                'attendanceCount' => Attendance::count(),
                'studentCount' => Student::count(),
            ]);
        }

        return view('dashboard', [
            'studentCount' => Student::count(),
            'employeeCount' => Employee::count(),
            'attendanceCount' => Attendance::count(),
            'expenseCount' => Expense::count(),
            'dueInvoices' => FeeInvoice::where('status', '!=', 'Paid')->count(),
            'recentAttendances' => Attendance::with('student')->latest()->limit(5)->get(),
            'recentInvoices' => FeeInvoice::with('student')->latest()->limit(5)->get(),
        ]);
    }
}
