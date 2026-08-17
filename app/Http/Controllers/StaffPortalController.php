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
        
        $studentCount = \App\Models\Student::count();
        $attendanceCount = \App\Models\Attendance::count();

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

        $effectiveLateDays = max($lateDays, $lateCheckInDays);

        // ── Salary Calculation ──────────────────────────────────────────
        $monthlySalary  = (float) ($employee->salary ?? 0);
        $workingDaysInMonth = 30;
        $allowedLeaves  = 2;

        if ($monthlySalary > 0) {
            $dailySalary      = $monthlySalary / $workingDaysInMonth;
            $halfDayDeduction = $dailySalary / 2;
        } else {
            $dailySalary      = 500;
            $halfDayDeduction = 250;
        }

        $unpaidLeaveDays  = max(0, $leaveDays - $allowedLeaves);
        $lateDeduction    = $effectiveLateDays * $halfDayDeduction;
        $absentDeduction  = $absentDays * $dailySalary;
        $unpaidLeaveDeduction = $unpaidLeaveDays * $dailySalary;
        $totalDeductions  = $lateDeduction + $absentDeduction + $unpaidLeaveDeduction;
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

        $recentMessages = \App\Models\Message::forUser($userId, session('user_role_slug'))->latest()->limit(5)->get();
        $unreadMessageCount = \App\Models\Message::forUser($userId, session('user_role_slug'))->unread()->count();

        // ── Today's Academic Schedule ──────────────────────────────────
        $departmentId = null;
        if ($employee->department) {
            $dept = \App\Models\Department::where('department_name', 'like', '%' . $employee->department . '%')->first();
            if ($dept) {
                $departmentId = $dept->id;
            }
        }

        $todayMeetings = \App\Models\Meeting::where(function($q) use ($departmentId, $userId) {
            if ($departmentId) {
                $q->where('department_id', $departmentId);
            }
            $q->orWhere('created_by', $userId)
              ->orWhereHas('participants', function($p) use ($userId) {
                  $p->where('user_id', $userId);
              });
        })
        ->whereDate('meeting_date', Carbon::today()->toDateString())
        ->orderBy('start_time', 'asc')
        ->get();

        // ── Today's Attendance Realtime Status ────────────────────────
        $todayDate = $now->toDateString();
        $todayAttendance = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $todayDate)
            ->first();

        $todayCheckIn = $todayAttendance?->check_in_time ? Carbon::parse($todayAttendance->check_in_time)->format('h:i A') : null;
        $todayCheckOut = $todayAttendance?->check_out_time ? Carbon::parse($todayAttendance->check_out_time)->format('h:i A') : null;
        $isFullyCompleted = ($todayCheckIn && $todayCheckOut);
        $canCheckIn = !$todayAttendance || !$todayAttendance->check_in_time;
        $canCheckOut = $todayAttendance && $todayAttendance->check_in_time && !$todayAttendance->check_out_time;

        $todayWorkedDuration = null;
        if ($todayAttendance && $todayAttendance->check_in_time) {
            try {
                $inTime = Carbon::parse($todayDate . ' ' . $todayAttendance->check_in_time);
                $outTime = $todayAttendance->check_out_time ? Carbon::parse($todayDate . ' ' . $todayAttendance->check_out_time) : Carbon::now();
                if ($outTime->gte($inTime)) {
                    $diffMins = $inTime->diffInMinutes($outTime);
                    $hrs = intdiv($diffMins, 60);
                    $mins = $diffMins % 60;
                    $todayWorkedDuration = ($hrs > 0 ? "{$hrs}h " : "") . "{$mins}m";
                }
            } catch (\Exception $e) {}
        }

        return view('portal.staff.dashboard', compact(
            'employee',
            'studentCount', 'attendanceCount',
            'attendances',
            'monthAttendances',
            'presentDays', 'absentDays', 'leaveDays', 'lateDays',
            'effectiveLateDays',
            'attendancePercentage',
            'dailySalary', 'halfDayDeduction',
            'lateDeduction', 'absentDeduction', 'unpaidLeaveDeduction', 'totalDeductions',
            'monthlySalary', 'netMonthlySalary',
            'unpaidLeaveDays', 'allowedLeaves',
            'joiningDate', 'experience',
            'assignedTasks', 'todayUpdate',
            'salarySlips', 'offerLetters', 'leaveApplications',
            'incomeRecords', 'totalIncome', 'unreadMessageCount', 'recentMessages',
            'todayMeetings',
            'todayAttendance', 'todayCheckIn', 'todayCheckOut', 'canCheckIn', 'canCheckOut', 'isFullyCompleted', 'todayWorkedDuration'
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

    public function updateProfile(Request $request)
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();
        $user = $employee->user;

        $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $filename);

            // Delete old photo if it exists
            if ($user->profile_pic && $user->profile_pic !== 'default.png' && file_exists(public_path('uploads/profiles/' . $user->profile_pic))) {
                @unlink(public_path('uploads/profiles/' . $user->profile_pic));
            }

            $user->profile_pic = $filename;
            $user->save();
        }

        $employee->address = $request->input('address');
        $employee->bio = $request->input('bio');
        $employee->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'photo_url' => $user->profile_pic ? asset('uploads/profiles/' . $user->profile_pic) : null,
                'address' => $employee->address,
                'bio' => $employee->bio,
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function punchAttendance(Request $request)
    {
        $userId = session('user_id');
        $employee = Employee::where('user_id', $userId)->firstOrFail();

        $today = date('Y-m-d');
        $nowTime = date('H:i:s');
        $displayTime = date('h:i A');

        $existing = EmployeeAttendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        // If already both checked in and checked out
        if ($existing && $existing->check_in_time && $existing->check_out_time) {
            $msg = 'Your attendance for today is already completed (In: ' . Carbon::parse($existing->check_in_time)->format('h:i A') . ', Out: ' . Carbon::parse($existing->check_out_time)->format('h:i A') . ').';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return back()->with('info', $msg);
        }

        $isCheckOut = ($existing && $existing->check_in_time && !$existing->check_out_time);
        $status = (strtotime($nowTime) > strtotime('10:00:00')) ? 'Late' : 'Present';

        if ($isCheckOut) {
            $existing->update([
                'check_out_time' => $nowTime,
                'device_name' => $existing->device_name ? $existing->device_name . ', Web Portal (Staff Console)' : 'Web Portal (Staff Console)',
            ]);
            $message = "Punch Out recorded successfully at {$displayTime}!";
            $action = 'check_out';
        } else {
            $existing = EmployeeAttendance::updateOrCreate(
                ['employee_id' => $employee->id, 'attendance_date' => $today],
                [
                    'check_in_time' => $nowTime,
                    'status' => $status,
                    'device_name' => 'Web Portal (Staff Console)',
                ]
            );
            $message = "Punch In recorded successfully at {$displayTime} (" . ($status === 'Late' ? 'Late Check-in' : 'On Time') . ")!";
            $action = 'check_in';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'action' => $action,
                'message' => $message,
                'check_in' => $existing->check_in_time ? Carbon::parse($existing->check_in_time)->format('h:i A') : null,
                'check_out' => $existing->check_out_time ? Carbon::parse($existing->check_out_time)->format('h:i A') : null,
                'status' => $existing->status,
                'time' => $displayTime
            ]);
        }

        return back()->with('success', $message);
    }
}
