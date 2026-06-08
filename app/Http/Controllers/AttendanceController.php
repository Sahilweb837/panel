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

    public function live()
    {
        // Get today's attendances for both students and staff
        $studentAttendances = Attendance::with('student')->whereDate('attendance_date', date('Y-m-d'))->latest()->get();
        $staffAttendances = \App\Models\EmployeeAttendance::with('employee')->whereDate('attendance_date', date('Y-m-d'))->latest()->get();

        $allAttendances = collect();
        
        foreach ($studentAttendances as $a) {
            $allAttendances->push((object)[
                'id' => 's_'.$a->id,
                'name' => $a->student->first_name . ' ' . $a->student->last_name,
                'role' => 'Student',
                'time' => $a->created_at->format('h:i A'),
                'timestamp' => $a->created_at,
                'status' => $a->status,
                'photo' => $a->photo_path,
                'device' => $a->device_name,
                'fine' => $a->fine
            ]);
        }

        foreach ($staffAttendances as $a) {
            $allAttendances->push((object)[
                'id' => 'e_'.$a->id,
                'name' => $a->employee->staff_name ?? $a->employee->employee_code,
                'role' => 'Staff',
                'time' => $a->created_at->format('h:i A'),
                'timestamp' => $a->created_at,
                'status' => $a->status,
                'photo' => $a->photo_path,
                'device' => $a->device_name,
                'fine' => 0
            ]);
        }

        $allAttendances = $allAttendances->sortByDesc('timestamp')->values();

        // Dashboard stats
        $presentToday = $allAttendances->where('status', 'Present')->count();
        $absentToday = $allAttendances->where('status', 'Absent')->count();
        $lateToday = $allAttendances->where('status', 'Late')->count();
        $faceCaptures = $allAttendances->whereNotNull('photo')->count();

        if (request()->ajax()) {
            $html = view('attendances.partials.live_table', compact('allAttendances'))->render();
            return response()->json([
                'html' => $html,
                'stats' => [
                    'present' => $presentToday,
                    'absent' => $absentToday,
                    'late' => $lateToday,
                    'face' => $faceCaptures
                ]
            ]);
        }

        return view('attendances.live', compact('allAttendances', 'presentToday', 'absentToday', 'lateToday', 'faceCaptures'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', \Carbon\Carbon::today()->toDateString());
        $students = Student::with('course')->where('status', true)->orderBy('first_name')->get();
        
        $existingAttendances = Attendance::whereDate('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('attendances.create', compact('students', 'date', 'existingAttendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:Present,Absent,Late,Leave'],
            'attendance.*.check_in_time' => ['nullable', 'string'],
            'attendance.*.check_out_time' => ['nullable', 'string'],
            'attendance.*.fine' => ['nullable', 'numeric', 'min:0'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:255'],
            'attendance.*.photo' => ['nullable', 'string'],
        ]);

        $date = $request->attendance_date;

        foreach ($request->attendance as $studentId => $data) {
            $photoPath = null;
            if (isset($data['photo']) && !empty($data['photo'])) {
                $image_parts = explode(";base64,", $data['photo']);
                if (count($image_parts) == 2) {
                    $image_type_aux = explode("image/", $image_parts[0]);
                    $image_type = $image_type_aux[1] ?? 'jpeg';
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = 'attendance_faces/student_' . $studentId . '_' . uniqid() . '.' . $image_type;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                    $photoPath = 'storage/' . $fileName;
                }
            }

            $updateData = [
                'status' => $data['status'],
                'check_in_time' => $data['check_in_time'] ?? null,
                'check_out_time' => $data['check_out_time'] ?? null,
                'fine' => $data['fine'] ?? 0,
                'remarks' => $data['remarks'] ?? null,
            ];

            if ($photoPath) {
                $updateData['photo_path'] = $photoPath;
            }

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                ],
                $updateData
            );
        }

        return redirect()->route('attendances.index')
            ->with('success', 'Student attendance recorded successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Attendance record removed successfully.');
    }

    public function exportCsv(Request $request)
    {
        $query = Attendance::with('student.course');

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

        $attendances = $query->latest('attendance_date')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=student_attendance_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Admission No', 'Student Name', 'Course', 'Attendance Date', 'Status', 'Check-in Time', 'Check-out Time', 'Fine', 'Remarks']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->student?->admission_no ?? '-',
                    ($attendance->student?->first_name ?? '') . ' ' . ($attendance->student?->last_name ?? ''),
                    $attendance->student?->course?->name ?? '-',
                    $attendance->attendance_date,
                    $attendance->status,
                    $attendance->check_in_time ?? '-',
                    $attendance->check_out_time ?? '-',
                    number_format($attendance->fine, 2),
                    $attendance->remarks ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Attendance::with('student.course');

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

        $attendances = $query->latest('attendance_date')->get();

        return view('attendances.print', compact('attendances'));
    }
}
