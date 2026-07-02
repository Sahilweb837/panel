<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\StudentMilestone;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $userId = session('user_id');
        $student = Student::with(['course', 'user'])->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('login')->withErrors(['email' => 'No student profile associated with this account.']);
        }

        $attendances = Attendance::where('student_id', $student->id)->latest()->limit(30)->get();

        $presentDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $lateDays = $attendances->where('status', 'Late')->count();
        $totalDays = $attendances->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        $courses = Course::where('status', true)->get();

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

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $attendanceFineRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where(function($query) {
                $query->where('status', 'Absent')
                      ->orWhere('fine', '>', 0);
            })
            ->get();

        $biometricFine = 0;
        $fineDetailsList = [];
        foreach ($attendanceFineRecords as $att) {
            $fineAmount = $att->fine > 0 ? (float)$att->fine : 50;
            if ($att->status === 'Absent' || $att->fine > 0) {
                $biometricFine += $fineAmount;
                $fineDetailsList[] = Carbon::parse($att->attendance_date)->format('M d') . ' (\u20B9' . $fineAmount . ')';
            }
        }
        $fineDetails = implode(', ', $fineDetailsList);

        $invoices = FeeInvoice::where('student_id', $student->id)->latest()->get();

        $feeInvoices = FeeInvoice::where('student_id', $student->id)->latest()->limit(10)->get();
        $dueFees = $feeInvoices->sum('due_amount');
        $paidFees = $feeInvoices->sum('paid_amount');
        $totalFees = $feeInvoices->sum('total_amount');

        $milestones = StudentMilestone::where('student_id', $student->id)
            ->orderByRaw("FIELD(status, 'In Progress', 'Upcoming', 'Completed', 'Skipped')")
            ->orderBy('target_date', 'asc')
            ->get();

        $completedMilestones = $milestones->where('status', 'Completed')->count();
        $totalMilestones = $milestones->count();
        $milestoneProgress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;

        return view('portal.student.dashboard', compact(
            'student', 'attendances', 'presentDays', 'absentDays', 'lateDays',
            'attendancePercentage', 'courses', 'monthlyCourseFee', 'monthlyDiscount',
            'netMonthlyFee', 'biometricFine', 'fineDetails', 'invoices',
            'dueFees', 'paidFees', 'totalFees', 'milestones', 'completedMilestones',
            'totalMilestones', 'milestoneProgress'
        ));
    }

    public function selectCourse(Request $request)
    {
        $userId = session('user_id');
        $student = Student::where('user_id', $userId)->first();

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $course = Course::findOrFail($request->course_id);
        $student->course_id = $course->id;
        $student->course_duration = $course->duration ?: '1 Year';
        $student->save();

        return redirect()->route('student.dashboard')->with('success', 'You have successfully enrolled/updated your course to: ' . $course->name);
    }
}
