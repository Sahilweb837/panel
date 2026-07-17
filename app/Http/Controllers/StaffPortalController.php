<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\Employee;
use App\Models\StaffOfferLetter;
use App\Models\LeaveApplication;
use App\Models\StaffIncomeRecord;
use App\Models\SalarySlip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffPortalController extends Controller
{
    public function dashboard()
    {
        $userId = session('user_id');
        $employee = Employee::with('user')->where('user_id', $userId)->first();

        if (!$employee) {
            return redirect()->route('login')->withErrors(['email' => 'No staff profile associated with this account.']);
        }

        // ── Current month attendance ─────────────────────────────────────
        $now          = Carbon::now();
        $monthStart   = $now->copy()->startOfMonth()->toDateString();
        $monthEnd     = $now->copy()->endOfMonth()->toDateString();

        $monthAttendances = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->orderBy('attendance_date', 'desc')
            ->get();

        $presentDays    = $monthAttendances->where('status', 'Present')->count();
        $absentDays     = $monthAttendances->where('status', 'Absent')->count();
        $lateDays       = $monthAttendances->where('status', 'Late')->count();
        $leaveDays      = $monthAttendances->where('status', 'Leave')->count();
        $totalMarked    = $monthAttendances->count();

        $attendancePercentage = $totalMarked > 0 ? round(($presentDays / $totalMarked) * 100) : 0;

        // ── Late-check-in detection (after 10:00 AM) ────────────────────
        // For attendance records that have check_in_time but aren't already marked "Late"
        $lateCheckInDays = $monthAttendances->filter(function ($att) {
            if (!$att->check_in_time) return false;
            try {
                $checkIn = Carbon::parse($att->attendance_date . ' ' . $att->check_in_time);
                $cutOff  = Carbon::parse($att->attendance_date . ' 10:00:00');
                return $checkIn->gt($cutOff);
            } catch (\Exception $e) {
                return false;
            }
        })->count();

        // Use max of marked Late days vs detected late-check-in days
        $effectiveLateDays = max($lateDays, $lateCheckInDays);

        // ── Salary Calculation ──────────────────────────────────────────
        $monthlySalary  = (float) ($employee->salary ?? 0);
        $workingDaysInMonth = 26; // Standard working days per month

        if ($monthlySalary > 0) {
            $dailySalary      = $monthlySalary / $workingDaysInMonth;
            $halfDayDeduction = $dailySalary / 2;
        } else {
            $dailySalary      = 500;   // Default if no salary set
            $halfDayDeduction = 250;
        }

        $lateDeduction    = $effectiveLateDays * $halfDayDeduction;
        $absentDeduction  = $absentDays * $dailySalary;
        $totalDeductions  = $lateDeduction + $absentDeduction;
        $netMonthlySalary = max(0, $monthlySalary > 0 ? $monthlySalary - $totalDeductions : 0);

        // ── Experience Calculation ─────────────────────────────────────
        $joiningDate  = $employee->joining_date ? Carbon::parse($employee->joining_date) : null;
        $experience   = $joiningDate ? $joiningDate->diff($now) : null;

        // ── Tasks ───────────────────────────────────────────────────────
        $assignedTasks = \App\Models\Task::where('assigned_to', $employee->id)
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'Completed')")
            ->orderBy('due_date', 'asc')
            ->get();

        // ── Today's Update ──────────────────────────────────────────────
        $todayUpdate = \App\Models\DailyUpdate::where('employee_id', $employee->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        // ── Salary Slips ────────────────────────────────────────────────
        $salarySlips = SalarySlip::where('employee_id', $employee->id)->latest('created_at')->get();

        // ── Other records ───────────────────────────────────────────────
        $offerLetters      = StaffOfferLetter::where('employee_id', $employee->id)->latest()->get();
        $leaveApplications = LeaveApplication::where('employee_id', $employee->id)->latest()->get();
        $incomeRecords     = StaffIncomeRecord::where('employee_id', $employee->id)->latest()->get();
        $totalIncome       = $incomeRecords->where('status', 'Received')->sum('amount');

        // ── Attendance for last 30 days (for history table) ─────────────
        $attendances = EmployeeAttendance::where('employee_id', $employee->id)
            ->latest('attendance_date')->limit(30)->get();

        return view('portal.staff.dashboard', compact(
            'employee',
            'attendances',
            'monthAttendances',
            'presentDays', 'absentDays', 'lateDays', 'leaveDays',
            'effectiveLateDays',
            'attendancePercentage',
            'dailySalary', 'halfDayDeduction',
            'lateDeduction', 'absentDeduction', 'totalDeductions',
            'monthlySalary', 'netMonthlySalary',
            'joiningDate', 'experience',
            'assignedTasks', 'todayUpdate',
            'salarySlips', 'offerLetters', 'leaveApplications',
            'incomeRecords', 'totalIncome',
        ));
    }

    public function offerLetters()
    {
        $userId   = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $offerLetters = StaffOfferLetter::where('employee_id', $employee->id)->latest()->get();
        return view('portal.staff.offer-letters', compact('employee', 'offerLetters'));
    }

    public function leaveApplications()
    {
        $userId   = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $leaves   = LeaveApplication::where('employee_id', $employee->id)->latest()->get();
        return view('portal.staff.leave', compact('employee', 'leaves'));
    }

    public function incomeRecords()
    {
        $userId       = session('user_id');
        $employee     = Employee::where('user_id', $userId)->firstOrFail();
        $incomeRecords = StaffIncomeRecord::where('employee_id', $employee->id)->latest()->get();
        $totalIncome   = $incomeRecords->where('status', 'Received')->sum('amount');
        return view('portal.staff.income', compact('employee', 'incomeRecords', 'totalIncome'));
    }
}
