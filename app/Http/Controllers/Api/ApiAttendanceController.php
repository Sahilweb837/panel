<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiAttendanceController extends Controller
{
    /**
     * Display a listing of attendance logs.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['student:id,first_name,last_name,admission_no,course_id', 'student.course:id,name']);

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        } else {
            $query->whereDate('attendance_date', now()->toDateString());
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('check_in_time', 'desc')->get();

        return response()->json([
            'success' => true,
            'count'   => $records->count(),
            'date'    => $request->input('date', now()->toDateString()),
            'data'    => $records
        ]);
    }

    /**
     * Punch / record attendance for a student (manual, biometric or API).
     */
    public function record(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'      => 'nullable|exists:students,id',
            'admission_no'    => 'nullable|string',
            'status'          => 'required|in:Present,Absent,Late,Leave',
            'attendance_date' => 'nullable|date',
            'check_in_time'   => 'nullable',
            'check_out_time'  => 'nullable',
            'device_name'     => 'nullable|string',
            'remarks'         => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $studentId = $request->student_id;
        if (!$studentId && $request->filled('admission_no')) {
            $cleanCode = str_ireplace(['NT-ENR-', 'NT-'], '', $request->admission_no);
            $student = Student::where('admission_no', $request->admission_no)
                ->orWhere('roll_no', $request->admission_no)
                ->orWhereRaw("REPLACE(REPLACE(admission_no, 'NT-ENR-', ''), 'NT-', '') = ?", [$cleanCode])
                ->first();

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found with this ID.'], 404);
            }
            $studentId = $student->id;
        }

        if (!$studentId) {
            return response()->json(['success' => false, 'message' => 'student_id or admission_no required.'], 422);
        }

        $attendanceDate = $request->attendance_date ? Carbon::parse($request->attendance_date)->toDateString() : now()->toDateString();
        $checkIn = $request->check_in_time ? Carbon::parse($request->check_in_time)->format('H:i:s') : now()->format('H:i:s');

        $record = Attendance::updateOrCreate(
            [
                'student_id'      => $studentId,
                'attendance_date' => $attendanceDate,
            ],
            [
                'status'         => $request->status,
                'check_in_time'  => $checkIn,
                'check_out_time' => $request->check_out_time ? Carbon::parse($request->check_out_time)->format('H:i:s') : null,
                'device_name'    => $request->device_name ?? 'API Gateway',
                'remarks'        => $request->remarks,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully.',
            'data'    => $record->load('student:id,first_name,last_name,admission_no')
        ]);
    }

    /**
     * Get attendance summary for a specific student.
     */
    public function studentSummary($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $attendances = Attendance::where('student_id', $studentId)->get();
        $total = $attendances->count();
        $present = $attendances->where('status', 'Present')->count();
        $late = $attendances->where('status', 'Late')->count();
        $absent = $attendances->where('status', 'Absent')->count();
        $leave = $attendances->where('status', 'Leave')->count();

        $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => trim($student->first_name.' '.$student->last_name),
                'admission_no' => $student->admission_no,
            ],
            'summary' => [
                'total_days'  => $total,
                'present'     => $present,
                'late'        => $late,
                'absent'      => $absent,
                'leave'       => $leave,
                'rate_percent'=> $percentage,
            ]
        ]);
    }
}

