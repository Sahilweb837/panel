<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FaceAttendanceController extends Controller
{
    public function captureView()
    {
        $role = session('user_role_slug');
        $today = date('Y-m-d');

        if ($role === 'student') {
            $user = Student::with('user')->where('user_id', session('user_id'))->first();
            $type = 'student';
            $referencePhoto = $user && $user->photo ? Storage::url($user->photo) : asset('image.png');
            
            $existing = $user ? Attendance::where('student_id', $user->id)->whereDate('attendance_date', $today)->first() : null;
        } else {
            $user = Employee::with('user')->where('user_id', session('user_id'))->first();
            $type = 'staff';
            $referencePhoto = ($user && $user->user && $user->user->profile_pic && $user->user->profile_pic !== 'default.png') 
                                ? asset('uploads/profiles/' . $user->user->profile_pic) 
                                : asset('image.png');
                                
            $existing = $user ? EmployeeAttendance::where('employee_id', $user->id)->whereDate('attendance_date', $today)->first() : null;
        }

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Profile not found.']);
        }

        // Determine action: check_in, check_out, or completed
        $actionType = 'check_in';
        $checkInTimeFormatted = null;
        $checkOutTimeFormatted = null;

        if ($existing) {
            if ($existing->check_in_time) {
                $checkInTimeFormatted = Carbon::parse($existing->check_in_time)->format('h:i A');
            }
            if ($existing->check_out_time) {
                $checkOutTimeFormatted = Carbon::parse($existing->check_out_time)->format('h:i A');
            }

            if ($existing->check_in_time && $existing->check_out_time) {
                $actionType = 'completed';
            } elseif ($existing->check_in_time) {
                $actionType = 'check_out';
            }
        }

        $alreadyMarked = ($actionType === 'completed');

        return view('portal.attendance.capture', compact(
            'user', 
            'type', 
            'referencePhoto', 
            'alreadyMarked', 
            'actionType', 
            'checkInTimeFormatted', 
            'checkOutTimeFormatted',
            'existing'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'type' => 'required|in:student,staff',
            'id' => 'required|integer',
        ]);

        $today = date('Y-m-d');
        $nowTime = date('H:i:s');
        $displayTime = date('h:i A');

        if ($request->type === 'student') {
            $existing = Attendance::where('student_id', $request->id)
                                ->whereDate('attendance_date', $today)
                                ->first();
        } else {
            $existing = EmployeeAttendance::where('employee_id', $request->id)
                                ->whereDate('attendance_date', $today)
                                ->first();
        }

        // If already both checked in and checked out
        if ($existing && $existing->check_in_time && $existing->check_out_time) {
            return response()->json([
                'success' => false, 
                'message' => 'Attendance for today is already completed (Checked in at ' . Carbon::parse($existing->check_in_time)->format('h:i A') . ', Checked out at ' . Carbon::parse($existing->check_out_time)->format('h:i A') . ')!'
            ]);
        }

        // Determine action
        $isCheckOut = ($existing && $existing->check_in_time && !$existing->check_out_time);

        // Process image upload
        $image = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '_' . $request->type . '_' . $request->id . '_' . ($isCheckOut ? 'out' : 'in') . '.jpg';

        $monthYear = date('Y/m');
        $relPath = "attendances/{$monthYear}/{$imageName}";
        Storage::disk('public')->put($relPath, base64_decode($image));
        $fullPhotoPath = "storage/{$relPath}";

        $status = (strtotime($nowTime) > strtotime('10:00:00')) ? 'Late' : 'Present';

        if ($request->type === 'student') {
            if ($isCheckOut) {
                $existing->update([
                    'check_out_time' => $nowTime,
                    'photo_path' => $fullPhotoPath,
                    'device_name' => 'Web Portal AI Camera (Punch Out)',
                ]);
                $message = "Punch Out recorded successfully at {$displayTime}!";
            } else {
                Attendance::updateOrCreate(
                    ['student_id' => $request->id, 'attendance_date' => $today],
                    [
                        'check_in_time' => $nowTime,
                        'status' => $status,
                        'photo_path' => $fullPhotoPath,
                        'device_name' => 'Web Portal AI Camera (Punch In)',
                    ]
                );
                $message = "Punch In recorded successfully at {$displayTime} (" . ($status === 'Late' ? 'Late Check-in' : 'On Time') . ")!";
            }
        } else {
            if ($isCheckOut) {
                $existing->update([
                    'check_out_time' => $nowTime,
                    'photo_path' => $fullPhotoPath,
                    'device_name' => 'Web Portal AI Camera (Punch Out)',
                ]);
                $message = "Staff Punch Out recorded successfully at {$displayTime}!";
            } else {
                EmployeeAttendance::updateOrCreate(
                    ['employee_id' => $request->id, 'attendance_date' => $today],
                    [
                        'check_in_time' => $nowTime,
                        'status' => $status,
                        'photo_path' => $fullPhotoPath,
                        'device_name' => 'Web Portal AI Camera (Punch In)',
                    ]
                );
                $message = "Staff Punch In recorded successfully at {$displayTime} (" . ($status === 'Late' ? 'Late Check-in' : 'On Time') . ")!";
            }
        }

        return response()->json([
            'success' => true, 
            'action' => $isCheckOut ? 'check_out' : 'check_in',
            'message' => $message,
            'time' => $displayTime
        ]);
    }
}
