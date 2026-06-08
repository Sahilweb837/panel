<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiometricDevice;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Log;

class ZKTecoADMSController extends Controller
{
    // GET /iclock/cdata?SN=...
    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        // Update last sync of the device if we want
        $device = BiometricDevice::first();
        if ($device) {
            $device->update(['last_sync' => now()]);
        }
        
        // ZKTeco expects GET_OPTION or OK responses during init
        return response("GET OPTION FROM: {$sn}\nStamp=9999\nOpStamp=9999\nErrorDelay=60\nDelay=10\nTransTimes=00:00;14:00\nTransInterval=1\nTransFlag=1111000000\nTimeZone=5.5\nRealtime=1\nEncrypt=0\nServerVer=2.2.14\n", 200)
                ->header('Content-Type', 'text/plain');
    }

    // POST /iclock/cdata?SN=...&table=ATTLOG
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table');
        $device = BiometricDevice::first();
        if ($device) {
            $device->update(['last_sync' => now()]);
        }

        // The body contains plain text lines
        $body = $request->getContent();
        Log::info("ADMS Webhook [$table] from $sn: \n" . $body);

        if ($table === 'ATTLOG') {
            $lines = explode("\n", trim($body));
            $count = 0;

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                // Format: PIN\tTime\tState\tVerifyType\tWorkCode
                $parts = explode("\t", trim($line));
                if (count($parts) >= 2) {
                    $pin = trim($parts[0]);
                    $time = trim($parts[1]); // e.g., 2026-06-08 09:15:00
                    
                    $punchDate = date('Y-m-d', strtotime($time));
                    $punchTime = date('H:i:s', strtotime($time));

                    // 1. Try to find if it's a student
                    $student = Student::where('biometric_id', $pin)->first();
                    if ($student) {
                        $exists = Attendance::where('student_id', $student->id)
                                    ->whereDate('attendance_date', $punchDate)
                                    ->exists();
                        
                        if (!$exists) {
                            Attendance::create([
                                'student_id' => $student->id,
                                'attendance_date' => $punchDate,
                                'check_in_time' => $punchTime,
                                'status' => 'Present',
                                'device_name' => 'ADMS ZKTeco Device (' . $sn . ')',
                                'created_at' => $time
                            ]);
                            $count++;
                        }
                        continue;
                    }

                    // 2. Try to find if it's an employee
                    $employee = Employee::where('biometric_id', $pin)->first();
                    if ($employee) {
                        $exists = EmployeeAttendance::where('employee_id', $employee->id)
                                    ->whereDate('attendance_date', $punchDate)
                                    ->exists();
                        
                        if (!$exists) {
                            EmployeeAttendance::create([
                                'employee_id' => $employee->id,
                                'attendance_date' => $punchDate,
                                'check_in_time' => $punchTime,
                                'status' => 'Present',
                                'device_name' => 'ADMS ZKTeco Device (' . $sn . ')',
                                'created_at' => $time
                            ]);
                            $count++;
                        }
                    }
                }
            }
            // Return OK: count to clear records from the device
            return response("OK: {$count}\n", 200)->header('Content-Type', 'text/plain');
        }

        return response("OK\n", 200)->header('Content-Type', 'text/plain');
    }

    // GET /iclock/getrequest
    public function getRequest(Request $request)
    {
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    // POST /iclock/devicecmd
    public function deviceCmd(Request $request)
    {
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }
}
