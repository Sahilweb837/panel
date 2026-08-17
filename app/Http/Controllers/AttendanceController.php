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

    public function live(Request $request)
    {
        $today = date('Y-m-d');
        // Get today's attendances for both students and staff
        $studentAttendances = Attendance::with(['student.course'])->whereDate('attendance_date', $today)->latest('updated_at')->get();
        $staffAttendances = \App\Models\EmployeeAttendance::with(['employee.user'])->whereDate('attendance_date', $today)->latest('updated_at')->get();

        $allAttendances = collect();
        
        foreach ($studentAttendances as $a) {
            $studentName = trim(($a->student?->first_name ?? '') . ' ' . ($a->student?->last_name ?? ''));
            if (empty($studentName)) {
                $studentName = $a->student?->admission_no ?? 'Student #' . $a->student_id;
            }

            $checkIn = $a->check_in_time ? \Carbon\Carbon::parse($a->check_in_time)->format('h:i A') : null;
            $checkOut = $a->check_out_time ? \Carbon\Carbon::parse($a->check_out_time)->format('h:i A') : null;
            
            $duration = null;
            if ($a->check_in_time && $a->check_out_time) {
                try {
                    $in = \Carbon\Carbon::parse($today . ' ' . $a->check_in_time);
                    $out = \Carbon\Carbon::parse($today . ' ' . $a->check_out_time);
                    if ($out->gte($in)) {
                        $diffMins = $in->diffInMinutes($out);
                        $hours = intdiv($diffMins, 60);
                        $mins = $diffMins % 60;
                        $duration = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                    }
                } catch (\Exception $e) {}
            } elseif ($a->check_in_time && $a->status !== 'Absent') {
                try {
                    $in = \Carbon\Carbon::parse($today . ' ' . $a->check_in_time);
                    $now = \Carbon\Carbon::now();
                    if ($now->gte($in)) {
                        $diffMins = $in->diffInMinutes($now);
                        $hours = intdiv($diffMins, 60);
                        $mins = $diffMins % 60;
                        $duration = "Active (" . ($hours > 0 ? "{$hours}h " : "") . "{$mins}m)";
                    }
                } catch (\Exception $e) {}
            }

            // Clean photo path
            $photo = $a->photo_path;
            if ($photo && !str_starts_with($photo, 'http') && !str_starts_with($photo, 'storage/') && !str_starts_with($photo, 'uploads/')) {
                $photo = 'storage/' . ltrim($photo, '/');
            }

            $allAttendances->push((object)[
                'id' => 's_'.$a->id,
                'db_id' => $a->id,
                'name' => $studentName,
                'code' => $a->student?->admission_no ?? '-',
                'sub_title' => $a->student?->course?->name ?? 'Student',
                'role' => 'Student',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'duration' => $duration,
                'time' => $checkOut ?? $checkIn ?? $a->updated_at?->format('h:i A') ?? $a->created_at->format('h:i A'),
                'timestamp' => $a->updated_at ?? $a->created_at,
                'status' => $a->status,
                'photo' => $photo,
                'device' => $a->device_name ?? 'Web Portal',
                'fine' => $a->fine
            ]);
        }

        foreach ($staffAttendances as $a) {
            $staffName = $a->employee?->user?->name ?? $a->employee?->employee_code ?? 'Staff #' . $a->employee_id;
            $checkIn = $a->check_in_time ? \Carbon\Carbon::parse($a->check_in_time)->format('h:i A') : null;
            $checkOut = $a->check_out_time ? \Carbon\Carbon::parse($a->check_out_time)->format('h:i A') : null;
            
            $duration = null;
            if ($a->check_in_time && $a->check_out_time) {
                try {
                    $in = \Carbon\Carbon::parse($today . ' ' . $a->check_in_time);
                    $out = \Carbon\Carbon::parse($today . ' ' . $a->check_out_time);
                    if ($out->gte($in)) {
                        $diffMins = $in->diffInMinutes($out);
                        $hours = intdiv($diffMins, 60);
                        $mins = $diffMins % 60;
                        $duration = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                    }
                } catch (\Exception $e) {}
            } elseif ($a->check_in_time && $a->status !== 'Absent') {
                try {
                    $in = \Carbon\Carbon::parse($today . ' ' . $a->check_in_time);
                    $now = \Carbon\Carbon::now();
                    if ($now->gte($in)) {
                        $diffMins = $in->diffInMinutes($now);
                        $hours = intdiv($diffMins, 60);
                        $mins = $diffMins % 60;
                        $duration = "On Duty (" . ($hours > 0 ? "{$hours}h " : "") . "{$mins}m)";
                    }
                } catch (\Exception $e) {}
            }

            // Clean photo path or profile photo fallback
            $photo = $a->photo_path;
            if ($photo && !str_starts_with($photo, 'http') && !str_starts_with($photo, 'storage/') && !str_starts_with($photo, 'uploads/')) {
                $photo = 'storage/' . ltrim($photo, '/');
            } elseif (!$photo && $a->employee?->user?->profile_pic && $a->employee->user->profile_pic !== 'default.png') {
                $photo = 'uploads/profiles/' . $a->employee->user->profile_pic;
            }

            $allAttendances->push((object)[
                'id' => 'e_'.$a->id,
                'db_id' => $a->id,
                'name' => $staffName,
                'code' => $a->employee?->employee_code ?? '-',
                'sub_title' => $a->employee?->designation ?? $a->employee?->department ?? 'Faculty Staff',
                'role' => 'Staff',
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'duration' => $duration,
                'time' => $checkOut ?? $checkIn ?? $a->updated_at?->format('h:i A') ?? $a->created_at->format('h:i A'),
                'timestamp' => $a->updated_at ?? $a->created_at,
                'status' => $a->status,
                'photo' => $photo,
                'device' => $a->device_name ?? 'Web Portal',
                'fine' => 0
            ]);
        }

        $allAttendances = $allAttendances->sortByDesc('timestamp')->values();

        // Dashboard stats
        $presentToday = $allAttendances->where('status', 'Present')->count();
        $absentToday = $allAttendances->where('status', 'Absent')->count();
        $lateToday = $allAttendances->where('status', 'Late')->count();
        $faceCaptures = $allAttendances->whereNotNull('photo')->count();
        $staffCount = $allAttendances->where('role', 'Staff')->count();
        $studentCount = $allAttendances->where('role', 'Student')->count();

        $roleFilter = $request->query('role', 'all');
        $filteredAttendances = $allAttendances;
        if ($roleFilter === 'staff') {
            $filteredAttendances = $allAttendances->where('role', 'Staff')->values();
        } elseif ($roleFilter === 'student') {
            $filteredAttendances = $allAttendances->where('role', 'Student')->values();
        }

        if (request()->ajax()) {
            $html = view('attendances.partials.live_table', ['allAttendances' => $filteredAttendances])->render();
            return response()->json([
                'html' => $html,
                'total' => $filteredAttendances->count(),
                'stats' => [
                    'present' => $presentToday,
                    'absent' => $absentToday,
                    'late' => $lateToday,
                    'face' => $faceCaptures,
                    'staff' => $staffCount,
                    'students' => $studentCount
                ]
            ]);
        }

        return view('attendances.live', [
            'allAttendances' => $filteredAttendances,
            'presentToday' => $presentToday,
            'absentToday' => $absentToday,
            'lateToday' => $lateToday,
            'faceCaptures' => $faceCaptures,
            'staffCount' => $staffCount,
            'studentCount' => $studentCount,
            'currentRole' => $roleFilter
        ]);
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

            // Auto-set ₹50 fine for absent students (biometric absent fine)
            $fineForRecord = ($data['status'] === 'Absent') ? 50 : (float)($data['fine'] ?? 0);

            $updateData = [
                'status' => $data['status'],
                'check_in_time' => $data['check_in_time'] ?? null,
                'check_out_time' => $data['check_out_time'] ?? null,
                'fine' => $fineForRecord,
                'remarks' => $data['remarks'] ?? null,
            ];

            if ($photoPath) {
                $updateData['photo_path'] = $photoPath;
            }

            $attendanceRecord = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'attendance_date' => $date,
                ],
                $updateData
            );

            if ($data['status'] === 'Absent') {
                $fineAmount = 50.00;
                $existingFine = \App\Models\FeeInvoice::where('student_id', $studentId)
                    ->where('fee_category', 'Fine')
                    ->where('remarks', 'like', "%Auto-generated absent fine for {$date}%")
                    ->first();

                if (!$existingFine) {
                    \App\Models\FeeInvoice::create([
                        'student_id' => $studentId,
                        'invoice_no' => 'FIN-' . now()->format('ymdHi') . '-' . $studentId . '-' . rand(10,99),
                        'fee_category' => 'Fine',
                        'fee_items' => [['category' => 'Absent Fine', 'amount' => $fineAmount]],
                        'total_amount' => $fineAmount,
                        'paid_amount' => 0,
                        'discount' => 0,
                        'fine' => 0,
                        'due_amount' => $fineAmount,
                        'status' => 'Unpaid',
                        'remarks' => "Auto-generated absent fine for {$date}",
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
            }
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

    public function generateFines(Request $request)
    {
        \Illuminate\Support\Facades\Artisan::call('app:generate-absent-fines');
        
        return back()->with('success', 'Absent fines generation process completed successfully.');
    }
}
