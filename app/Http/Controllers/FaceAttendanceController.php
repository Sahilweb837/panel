<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeAttendance;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceAttendanceController extends Controller
{
    public function captureView()
    {
        $role = session('user_role_slug');
        if ($role === 'student') {
            $user = Student::with('user')->where('user_id', session('user_id'))->first();
            $type = 'student';
            $referencePhoto = $user->photo ? Storage::url($user->photo) : asset('image.png');
            
            // Check if already marked today
            $alreadyMarked = Attendance::where('student_id', $user->id)
                                ->whereDate('attendance_date', date('Y-m-d'))
                                ->exists();
        } else {
            $user = Employee::with('user')->where('user_id', session('user_id'))->first();
            $type = 'staff';
            $referencePhoto = ($user->user && $user->user->profile_pic !== 'default.png') 
                                ? Storage::url('profiles/' . $user->user->profile_pic) 
                                : asset('image.png');
                                
            // Check if already marked today
            $alreadyMarked = EmployeeAttendance::where('employee_id', $user->id)
                                ->whereDate('attendance_date', date('Y-m-d'))
                                ->exists();
        }

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Profile not found.']);
        }

        return view('portal.attendance.capture', compact('user', 'type', 'referencePhoto', 'alreadyMarked'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'type' => 'required|in:student,staff',
            'id' => 'required|integer',
        ]);

        if ($request->type === 'student') {
            $alreadyMarked = Attendance::where('student_id', $request->id)
                                ->whereDate('attendance_date', date('Y-m-d'))
                                ->exists();
        } else {
            $alreadyMarked = EmployeeAttendance::where('employee_id', $request->id)
                                ->whereDate('attendance_date', date('Y-m-d'))
                                ->exists();
        }

        if ($alreadyMarked) {
            return response()->json(['success' => false, 'message' => 'Attendance already marked for today!']);
        }

        $image = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time() . '_' . $request->type . '_' . $request->id . '.jpg';

        $monthYear = date('Y/m');
        $path = "attendances/{$monthYear}/{$imageName}";

        Storage::disk('public')->put($path, base64_decode($image));

        if ($request->type === 'student') {
            Attendance::create([
                'student_id' => $request->id,
                'attendance_date' => date('Y-m-d'),
                'status' => 'Present',
                'photo_path' => $path,
                'device_name' => 'Web Portal AI Camera',
            ]);
        } else {
            EmployeeAttendance::create([
                'employee_id' => $request->id,
                'attendance_date' => date('Y-m-d'),
                'status' => 'Present',
                'photo_path' => $path,
                'device_name' => 'Web Portal AI Camera',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Attendance marked successfully']);
    }
}
