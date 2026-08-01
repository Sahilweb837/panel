<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ZktecoController extends Controller
{
    /**
     * ADMS devices often send a GET request initially to check server connectivity
     * and request commands.
     */
    public function handleDeviceCheck(Request $request)
    {
        return response("OK");
    }

    /**
     * Handle the pushed attendance logs from the ZKTeco ADMS device.
     */
    public function receivePush(Request $request)
    {
        Log::info('ZKTeco Push Data:', $request->all());

        // Standard ADMS often sends data as raw text or specific form data.
        // You might need to parse based on your exact device's protocol.
        // Assuming it sends an array of attendance records or form fields:
        $biometric_id = $request->input('user_id') ?? $request->input('pin');
        $timestamp = $request->input('time') ?? $request->input('punch_time');

        if (!$biometric_id || !$timestamp) {
            return response("OK");
        }

        $punchTime = Carbon::parse($timestamp);
        $date = $punchTime->format('Y-m-d');
        $time = $punchTime->format('H:i:s');

        // Check if biometric ID belongs to an employee
        $employee = Employee::where('biometric_id', $biometric_id)->first();
        if ($employee) {
            $this->logEmployeeAttendance($employee->id, $date, $time);
            return response("OK");
        }

        // Check if biometric ID belongs to a student
        $student = Student::where('biometric_id', $biometric_id)->first();
        if ($student) {
            $this->logStudentAttendance($student->id, $date, $time);
            return response("OK");
        }

        return response("OK");
    }

    private function logEmployeeAttendance($employeeId, $date, $time)
    {
        $attendance = EmployeeAttendance::where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();

        if ($attendance) {
            // If already checked in, update check_out_time
            if (!$attendance->check_out_time || Carbon::parse($time)->gt(Carbon::parse($attendance->check_out_time))) {
                $attendance->update([
                    'check_out_time' => $time,
                ]);
            }
        } else {
            // First punch of the day
            $status = (strtotime($time) > strtotime('10:00:00')) ? 'Late' : 'Present';
            EmployeeAttendance::create([
                'employee_id' => $employeeId,
                'attendance_date' => $date,
                'check_in_time' => $time,
                'status' => $status,
            ]);
        }
    }

    private function logStudentAttendance($studentId, $date, $time)
    {
        $attendance = Attendance::where('student_id', $studentId)
            ->where('attendance_date', $date)
            ->first();

        if ($attendance) {
            // If already checked in, update check_out_time
            if (!$attendance->check_out_time || Carbon::parse($time)->gt(Carbon::parse($attendance->check_out_time))) {
                $attendance->update([
                    'check_out_time' => $time,
                ]);
            }
        } else {
            // First punch of the day
            $status = (strtotime($time) > strtotime('10:00:00')) ? 'Late' : 'Present';
            Attendance::create([
                'student_id' => $studentId,
                'attendance_date' => $date,
                'check_in_time' => $time,
                'status' => $status,
            ]);
        }
    }
}
