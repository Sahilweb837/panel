<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $userId = session('user_id');
        $student = Student::with('course')->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->route('login')->withErrors(['email' => 'No student profile associated with this account.']);
        }

        $attendances = Attendance::where('student_id', $student->id)->latest()->limit(30)->get();
        
        $presentDays = $attendances->where('status', 'Present')->count();
        $absentDays = $attendances->where('status', 'Absent')->count();
        $lateDays = $attendances->where('status', 'Late')->count();
        $totalDays = $attendances->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        return view('portal.student.dashboard', compact(
            'student', 'attendances', 'presentDays', 'absentDays', 'lateDays', 'attendancePercentage'
        ));
    }
}
