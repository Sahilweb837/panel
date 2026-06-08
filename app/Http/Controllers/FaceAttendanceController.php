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
            $user = Student::where('user_id', session('user_id'))->first();
            $type = 'student';
        } else {
            $user = Employee::where('user_id', session('user_id'))->first();
            $type = 'staff';
        }

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Profile not found.']);
        }

        return view('portal.attendance.capture', compact('user', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'type' => 'required|in:student,staff',
            'id' => 'required|integer',
        ]);

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
                'device_name' => 'Web Portal Camera',
            ]);
        } else {
            EmployeeAttendance::create([
                'employee_id' => $request->id,
                'attendance_date' => date('Y-m-d'),
                'status' => 'Present',
                'photo_path' => $path,
                'device_name' => 'Web Portal Camera',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Attendance marked successfully']);
    }
}
