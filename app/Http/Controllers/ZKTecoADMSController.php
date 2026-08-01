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
        // Removed TimeZone and Time overrides so the physical machine's time settings are respected.
        return response("GET OPTION FROM: {$sn}\nStamp=9999\nOpStamp=9999\nErrorDelay=60\nDelay=10\nTransTimes=00:00;14:00\nTransInterval=1\nTransFlag=1111000000\nRealtime=1\nEncrypt=0\nServerVer=2.2.14\n", 200)
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

        date_default_timezone_set('Asia/Kolkata');

        if ($table === 'ATTLOG') {
            $lines = explode("\n", trim($body));
            $count = 0;

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                // Format: PIN\tTime\tState\tVerifyType\tWorkCode
                $parts = explode("\t", trim($line));
                if (count($parts) >= 2) {
                    $pin = trim($parts[0]);
                    $rawTime = trim($parts[1]); 
                    $state = isset($parts[2]) ? trim($parts[2]) : '0';
                    $isCheckOut = ($state === '1' || $state === '5');

                    // Fix ZKTeco machine 30-minute backward offset
                    $timeObj = new \DateTime($rawTime);
                    $timeObj->modify('+30 minutes');
                    $time = $timeObj->format('Y-m-d H:i:s');

                    $punchDate = date('Y-m-d', strtotime($time));
                    $punchTime = date('H:i:s', strtotime($time));

                    // Check for Late (e.g. after 10:00:00 AM)
                    $punchStatus = (strtotime($punchTime) > strtotime('10:00:00')) ? 'Late' : 'Present';

                    // 1. Try to find if it's a student
                    $student = Student::where('biometric_id', $pin)->first();
                    if ($student) {
                        $attendance = Attendance::where('student_id', $student->id)
                            ->where('attendance_date', $punchDate)
                            ->first();

                        if (!$attendance) {
                            $attendance = Attendance::create([
                                'student_id' => $student->id,
                                'attendance_date' => $punchDate,
                                'check_in_time' => $isCheckOut ? null : $punchTime,
                                'check_out_time' => $isCheckOut ? $punchTime : null,
                                'status' => $punchStatus,
                                'device_name' => 'ADMS ZKTeco Device (' . $sn . ')',
                                'created_at' => $time
                            ]);
                        } else {
                            if ($isCheckOut) {
                                $attendance->update(['check_out_time' => $punchTime]);
                            } elseif (!$attendance->check_in_time) {
                                $attendance->update(['check_in_time' => $punchTime, 'status' => $punchStatus]);
                            }
                        }
                    }

                    // 2. Try to find if it's an employee
                    $employee = Employee::where('biometric_id', $pin)->first();
                    if ($employee) {
                        $attendance = EmployeeAttendance::where('employee_id', $employee->id)
                            ->where('attendance_date', $punchDate)
                            ->first();

                        if (!$attendance) {
                            $attendance = EmployeeAttendance::create([
                                'employee_id' => $employee->id,
                                'attendance_date' => $punchDate,
                                'check_in_time' => $isCheckOut ? null : $punchTime,
                                'check_out_time' => $isCheckOut ? $punchTime : null,
                                'status' => $punchStatus,
                                'device_name' => 'ADMS ZKTeco Device (' . $sn . ')',
                                'created_at' => $time
                            ]);
                        } else {
                            if ($isCheckOut) {
                                $attendance->update(['check_out_time' => $punchTime]);
                            } elseif (!$attendance->check_in_time) {
                                $attendance->update(['check_in_time' => $punchTime, 'status' => $punchStatus]);
                            }
                        }
                    }
                }
            }
            // Always return plain OK to acknowledge all lines and clear them from the device queue
            return response("OK\n", 200)->header('Content-Type', 'text/plain');
        }

        if ($table === 'ATTPHOTO' || $table === 'realtimephoto' || strpos($table, 'photo') !== false) {
            $pin = $request->query('PIN');

            // Generate a filename
            $filename = 'punch_' . ($pin ?? 'unknown') . '_' . time() . '.jpg';
            $path = public_path('storage/biometric_photos/' . $filename);

            if (!file_exists(public_path('storage/biometric_photos'))) {
                mkdir(public_path('storage/biometric_photos'), 0777, true);
            }

            file_put_contents($path, $body);

            // If we have a PIN, attach it to today's attendance record
            if ($pin) {
                $today = date('Y-m-d');
                $photoUrl = 'storage/biometric_photos/' . $filename;

                $student = Student::where('biometric_id', $pin)->first();
                if ($student) {
                    $attendance = Attendance::firstOrCreate(
                        ['student_id' => $student->id, 'attendance_date' => $today],
                        ['status' => 'Present', 'device_name' => 'ADMS ZKTeco Device (' . $sn . ')']
                    );
                    $attendance->update(['photo_path' => $photoUrl]);
                } else {
                    $employee = Employee::where('biometric_id', $pin)->first();
                    if ($employee) {
                        $attendance = EmployeeAttendance::firstOrCreate(
                            ['employee_id' => $employee->id, 'attendance_date' => $today],
                            ['status' => 'Present', 'device_name' => 'ADMS ZKTeco Device (' . $sn . ')']
                        );
                        $attendance->update(['photo_path' => $photoUrl]);
                    }
                }
            }

            return response("OK\n", 200)->header('Content-Type', 'text/plain');
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
