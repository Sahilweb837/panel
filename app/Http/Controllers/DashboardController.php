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
        $user = User::with(['role', 'employee', 'student'])->find(session('user_id'));

        if (!$user) {
            return redirect()->route('login');
        }

        $roleSlug = $user->role?->slug;

        if ($roleSlug === 'student') {
            return $this->studentDashboard($user);
        }

        if ($roleSlug === 'staff') {
            return $this->staffDashboard($user);
        }

        return $this->adminDashboard($user);
    }

    protected function adminDashboard($user)
    {
        $studentCount = Student::count();
        $employeeCount = Employee::count();
        $attendanceCount = Attendance::count();
        $expenseCount = Expense::count();
        $dueInvoices = FeeInvoice::where('status', '!=', 'Paid')->count();

        $recentAttendances = Attendance::with('student')->latest('attendance_date')->limit(5)->get();
        $recentInvoices = FeeInvoice::with('student')->latest()->limit(5)->get();
        $recentStudents = Student::with('course')->latest()->limit(4)->get();
        $recentStaff = Employee::with('user')->latest()->limit(4)->get();

        $totalIncome = FeeInvoice::sum('paid_amount');
        $totalExpense = Expense::sum('amount');
        $totalPendingFees = FeeInvoice::sum('due_amount');

        $biometricDevice = \App\Models\BiometricDevice::first();

        // Calculate Working Hours 10 to 5 stat (employees present between 10 AM and 5 PM today)
        $workingHoursEmployeesCount = \App\Models\EmployeeAttendance::whereDate('attendance_date', today())
            ->whereTime('check_in_time', '<=', '17:00:00')
            ->where(function($query) {
                $query->whereNull('check_out_time')
                      ->orWhereTime('check_out_time', '>=', '10:00:00');
            })
            ->count();

        return view('dashboard', compact(
            'studentCount', 'employeeCount', 'attendanceCount', 'expenseCount',
            'dueInvoices', 'recentAttendances', 'recentInvoices', 'recentStudents',
            'recentStaff', 'totalIncome', 'totalExpense', 'totalPendingFees', 'biometricDevice',
            'workingHoursEmployeesCount'
        ));
    }

    protected function staffDashboard($user)
    {
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

        $salarySlips = $employee
            ? SalarySlip::where('employee_id', $employee->id)->latest()->limit(6)->get()
            : collect();

        $recentMessages = \App\Models\Message::forUser($user->id, $user->role?->slug)->latest()->limit(5)->get();

        return view('staff.dashboard', compact(
            'employee', 'salarySlips', 'assignedTasks', 'todayUpdate', 'recentMessages'
        ));
    }

    protected function studentDashboard($user)
    {
        $student = $user->student;

        if (!$student) {
            return redirect()->route('login')->withErrors(['email' => 'No student profile associated with this account.']);
        }

        $attendances = Attendance::where('student_id', $student->id)->latest()->limit(30)->get();

        $presentDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $lateDays = $attendances->where('status', 'Late')->count();
        $totalDays = $attendances->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        $courses = \App\Models\Course::where('status', true)->get();

        $tenureLabel = $student->fee_tenure ?? '1 Year';
        $tenureMonths = match($tenureLabel) {
            '1 Month' => 1,
            '3 Months' => 3,
            '6 Months' => 6,
            '1 Year' => 12,
            default => 12,
        };
        $courseFee = $student->course ? $student->course->fee : 0;
        $discount = $student->discount ?? 0;

        $durationLower = strtolower($student->course_duration ?? '1 year');
        $courseMonths = match(true) {
            str_contains($durationLower, '1 year') || str_contains($durationLower, '12 month') => 12,
            str_contains($durationLower, '6 month') => 6,
            str_contains($durationLower, '3 month') => 3,
            str_contains($durationLower, '1 month') => 1,
            default => 12,
        };

        $divisor = max(1, (int) ceil($courseMonths / $tenureMonths));
        $monthlyCourseFee = round($courseFee / $divisor, 2);
        $monthlyDiscount = round($discount / $divisor, 2);
        $netMonthlyFee = max(0, $monthlyCourseFee - $monthlyDiscount);

        $biometricFine = 0;
        $fineDetailsList = [];
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();

        $attendanceFineRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where(function($query) {
                $query->where('status', 'Absent')
                      ->orWhere('fine', '>', 0);
            })
            ->get();

        foreach ($attendanceFineRecords as $att) {
            $fineAmount = $att->fine > 0 ? (float)$att->fine : 50;
            if ($att->status === 'Absent' || $att->fine > 0) {
                $biometricFine += $fineAmount;
                $fineDetailsList[] = \Carbon\Carbon::parse($att->attendance_date)->format('M d') . ' (₹' . $fineAmount . ')';
            }
        }
        $fineDetails = implode(', ', $fineDetailsList);

        $invoices = FeeInvoice::where('student_id', $student->id)->latest()->get();

        $recentMessages = \App\Models\Message::forUser($user->id, $user->role?->slug)->latest()->limit(5)->get();

        return view('portal.student.dashboard', compact(
            'student', 'attendances', 'presentDays', 'absentDays', 'lateDays',
            'attendancePercentage', 'courses', 'monthlyCourseFee', 'monthlyDiscount',
            'netMonthlyFee', 'biometricFine', 'fineDetails', 'invoices', 'recentMessages'
        ));
    }

    public function clearCache()
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        return back()->with('success', 'Application cache cleared successfully.');
    }
}
