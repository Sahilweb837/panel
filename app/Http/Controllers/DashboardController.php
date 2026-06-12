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

            $assignedTasks = $employee
                ? \App\Models\Task::where('assigned_to', $employee->id)
                    ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
                    ->orderBy('due_date', 'asc')
                    ->get()
                : collect();

            $todayUpdate = $employee
                ? \App\Models\DailyUpdate::where('employee_id', $employee->id)
                    ->whereDate('date', now()->toDateString())
                    ->first()
                : null;

            return view('staff.dashboard', [
                'employee' => $employee,
                'salarySlips' => $employee
                    ? SalarySlip::where('employee_id', $employee->id)->latest()->limit(6)->get()
                    : collect(),
                'attendanceCount' => Attendance::count(),
                'studentCount' => Student::count(),
                'assignedTasks' => $assignedTasks,
                'todayUpdate' => $todayUpdate,
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
            'totalIncome' => FeeInvoice::sum('paid_amount'),
            'totalExpense' => Expense::sum('amount'),
            'totalPendingFees' => FeeInvoice::sum('due_amount'),
            'biometricDevice' => \App\Models\BiometricDevice::first(),
        ]);
    }

    public function clearCache()
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        return back()->with('success', 'Application cache cleared successfully.');
    }
}
