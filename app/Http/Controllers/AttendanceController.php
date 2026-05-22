<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('student');

        if ($request->filled('student')) {
            $query->whereHas('student', function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->student.'%')
                    ->orWhere('last_name', 'like', '%'.$request->student.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->student.'%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        $attendances = $query->latest()->paginate(12)->withQueryString();
        $students = Student::orderBy('first_name')->get();

        return view('attendances.index', compact('attendances', 'students'));
    }

    public function create()
    {
        return view('attendances.create', [
            'students' => Student::orderBy('first_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'status' => ['required', 'in:Present,Absent,Late,Leave'],
            'attendance_date' => ['required', 'date'],
            'fine' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'Absent' && ! $request->filled('fine')) {
            $data['fine'] = 50;
        }

        $data['fine'] = $data['fine'] ?? 0;

        Attendance::updateOrCreate([
            'student_id' => $data['student_id'],
            'attendance_date' => $data['attendance_date'],
        ], [
            'status' => $data['status'],
            'fine' => $data['fine'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record removed successfully.');
    }
}
