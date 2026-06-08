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
        $device = BiometricDevice::first();
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
                    $exists = Attendance::where('student_id', $student->id)
                                ->whereDate('attendance_date', $punchDate)
                                ->exists();
                    
                    if (!$exists) {
                        Attendance::create([
                            'student_id' => $student->id,
                            'attendance_date' => $punchDate,
                            'status' => 'Present', // Basic check-in. Could add Late logic here based on time.
                            'device_name' => 'Biometric Device (' . $device->ip_address . ')',
                            'created_at' => $log['timestamp']
                        ]);
                        $newRecords++;
                    }
                    continue; // Done with this log
                }

                // 2. Try to find if it's an employee
                $employee = Employee::where('biometric_id', $log['id'])->first();
                if ($employee) {
                    $exists = EmployeeAttendance::where('employee_id', $employee->id)
                                ->whereDate('attendance_date', $punchDate)
                                ->exists();
                    
                    if (!$exists) {
                        EmployeeAttendance::create([
                            'employee_id' => $employee->id,
                            'attendance_date' => $punchDate,
                            'status' => 'Present',
                            'device_name' => 'Biometric Device (' . $device->ip_address . ')',
                            'created_at' => $log['timestamp']
                        ]);
                        $newRecords++;
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
