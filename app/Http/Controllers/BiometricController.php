<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiometricDevice;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class BiometricController extends Controller
{
    public function index()
    {
        try {
            $device = BiometricDevice::first();
        } catch (\Illuminate\Database\QueryException $e) {
            // If table does not exist, automatically run migrations on the live server
            if (str_contains($e->getMessage(), '1146')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                $device = BiometricDevice::first();
            } else {
                throw $e;
            }
        }

        if (!$device) {
            $device = BiometricDevice::create([
                'name' => 'Main Entrance Machine',
                'ip_address' => '192.168.1.201',
                'port' => 4370,
                'status' => true
            ]);
        }
        
        return view('biometric.index', compact('device'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string',
            'port' => 'required|numeric'
        ]);

        $device = BiometricDevice::first();
        $device->update([
            'ip_address' => $request->ip_address,
            'port' => $request->port
        ]);

        return back()->with('success', 'Biometric device settings updated.');
    }

    public function testConnection()
    {
        $device = BiometricDevice::first();
        $zk = new ZKTeco($device->ip_address, $device->port);
        
        if ($zk->connect()) {
            $zk->disconnect();
            return back()->with('success', 'Connection successful! The device is reachable on your network.');
        } else {
            return back()->withErrors(['connection' => 'Connection failed. Ensure the device is on the same WiFi network and the IP is correct.']);
        }
    }

    public function syncLogs()
    {
        $device = BiometricDevice::first();
        $zk = new ZKTeco($device->ip_address, $device->port);
        
        if (!$zk->connect()) {
            return back()->withErrors(['connection' => 'Could not connect to biometric device.']);
        }

        try {
            // Get all attendance logs from the machine
            $attendanceLogs = $zk->getAttendance();
            
            // It's good practice to clear logs after syncing if it's full, but we'll leave them for now.
            // $zk->clearAttendance();

            $zk->disconnect();

            $newRecords = 0;

            foreach ($attendanceLogs as $log) {
                // $log['id'] is the Biometric User ID
                // $log['timestamp'] is the datetime of the punch
                $punchDate = date('Y-m-d', strtotime($log['timestamp']));
                $punchTime = date('H:i:s', strtotime($log['timestamp']));
                
                // 1. Try to find if it's a student
                $student = Student::where('biometric_id', $log['id'])->first();
                if ($student) {
                    // Check if already marked for this date
                    $attendance = Attendance::where('student_id', $student->id)
                                ->whereDate('attendance_date', $punchDate)
                                ->first();
                    
                    if (!$attendance) {
                        Attendance::create([
                            'student_id' => $student->id,
                            'attendance_date' => $punchDate,
                            'check_in_time' => $punchTime,
                            'status' => 'Present', // Basic check-in. Could add Late logic here based on time.
                            'device_name' => 'Biometric Device (' . $device->ip_address . ')',
                            'created_at' => $log['timestamp']
                        ]);
                        $newRecords++;
                    } else {
                        if (strtotime($punchTime) > strtotime($attendance->check_in_time)) {
                            if (!$attendance->check_out_time || strtotime($punchTime) > strtotime($attendance->check_out_time)) {
                                $attendance->update(['check_out_time' => $punchTime]);
                                $newRecords++;
                            }
                        }
                    }
                    continue; // Done with this log
                }

                // 2. Try to find if it's an employee
                $employee = Employee::where('biometric_id', $log['id'])->first();
                if ($employee) {
                    $attendance = EmployeeAttendance::where('employee_id', $employee->id)
                                ->whereDate('attendance_date', $punchDate)
                                ->first();
                    
                    if (!$attendance) {
                        EmployeeAttendance::create([
                            'employee_id' => $employee->id,
                            'attendance_date' => $punchDate,
                            'check_in_time' => $punchTime,
                            'status' => 'Present',
                            'device_name' => 'Biometric Device (' . $device->ip_address . ')',
                            'created_at' => $log['timestamp']
                        ]);
                        $newRecords++;
                    } else {
                        if (strtotime($punchTime) > strtotime($attendance->check_in_time)) {
                            if (!$attendance->check_out_time || strtotime($punchTime) > strtotime($attendance->check_out_time)) {
                                $attendance->update(['check_out_time' => $punchTime]);
                                $newRecords++;
                            }
                        }
                    }
                }
            }
            
            $device->update(['last_sync' => now()]);

            return back()->with('success', "Sync completed successfully. {$newRecords} new attendance records imported.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error reading logs from device: ' . $e->getMessage()]);
        }
    }
}
