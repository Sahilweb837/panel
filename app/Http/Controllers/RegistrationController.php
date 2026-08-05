<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function showStudentRegistration()
    {
        $roles = Role::whereIn('slug', ['student'])->get();
        return view('auth.register-student', compact('roles'));
    }

    public function showStaffRegistration()
    {
        $roles = Role::whereIn('slug', ['staff'])->get();
        return view('auth.register-staff', compact('roles'));
    }

    public function registerStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20'],
            'admission_no' => ['required', 'string', 'max:255', 'unique:students,admission_no'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $studentRole = Role::firstOrCreate(['slug' => 'student'], ['role_name' => 'Student', 'description' => 'Enrolled student.']);

        DB::transaction(function () use ($request, $studentRole) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $studentRole->id,
                'status' => true,
                'phone_number' => $request->phone_number,
                'is_phone_verified' => false,
            ]);

            Student::create([
                'user_id' => $user->id,
                'admission_no' => $request->admission_no,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'address' => $request->address,
                'email' => $request->email,
                'status' => true,
            ]);
        });

        return redirect()->route('login')->with('success', 'Student registered successfully! You can now login.');
    }

    public function registerStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20'],
            'employee_code' => ['required', 'string', 'max:255', 'unique:employees,employee_code'],
            'designation' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'joining_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $staffRole = Role::firstOrCreate(['slug' => 'staff'], ['role_name' => 'Staff', 'description' => 'Teaching or office staff.']);

        DB::transaction(function () use ($request, $staffRole) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $staffRole->id,
                'status' => true,
                'phone_number' => $request->phone_number,
                'is_phone_verified' => false,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $request->employee_code,
                'designation' => $request->designation,
                'department' => $request->department,
                'phone' => $request->phone_number,
                'joining_date' => $request->joining_date,
                'status' => true,
            ]);
        });

        return redirect()->route('login')->with('success', 'Staff registered successfully! You can now login.');
    }
}
