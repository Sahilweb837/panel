<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }

        return view('auth.login');
    }

    public function showStudentLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }
        return view('auth.student-login');
    }

    public function showStaffLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }
        return view('auth.staff-login');
    }

    public function showAdminLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $loginId = trim($request->input('login_id', $request->input('email')));
        
        $request->validate([
            'account_type' => ['required', 'in:institute,staff,student'],
            'password' => ['required'],
        ]);

        if (empty($loginId)) {
            Session::flash('login_error', 'Please enter your email, username, or student admission ID.');
            return back()->withErrors(['email' => 'Please enter your email, username, or student admission ID.']);
        }

        $user = User::with('role')->where(function ($query) use ($loginId) {
            $query->where('email', $loginId)
                  ->orWhere('username', $loginId);
        })->first();

        // If not found by user table email/username, check student admission_no or roll_no
        if (!$user) {
            $studentMatch = \App\Models\Student::where('admission_no', $loginId)
                ->orWhere('roll_no', $loginId)
                ->first();
            if ($studentMatch && $studentMatch->user_id) {
                $user = User::with('role')->find($studentMatch->user_id);
            }
        }

        if (!$user) {
            Session::flash('login_error', 'No account found with this email, username, or student ID.');
            return back()->withErrors(['email' => 'No account found with this email, username, or student ID.'])->onlyInput('email');
        }

        // Always use Hash::check – password must match
        $passwordMatches = Hash::check($request->password, $user->password);

        if (!$passwordMatches) {
            Session::flash('login_error', 'Incorrect password. Please try again.');
            return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->onlyInput('email');
        }

        if (!$user->status) {
            Session::flash('login_error', 'Your account is currently inactive.');
            return back()->withErrors(['email' => 'Your account is currently inactive.'])->onlyInput('email');
        }

        $roleSlug = $user->role?->slug;

        // Fallback: If role_slug is incorrect but user has a student record
        if ($request->account_type === 'student' && $roleSlug !== 'student') {
            if (\App\Models\Student::where('user_id', $user->id)->exists()) {
                $roleSlug = 'student';
                // optionally fix the role_id in db
                $studentRole = \App\Models\Role::where('slug', 'student')->first();
                if ($studentRole) {
                    $user->update(['role_id' => $studentRole->id]);
                }
            }
        }

        // Fallback: If role_slug is incorrect but user has a staff record
        if ($request->account_type === 'staff' && $roleSlug !== 'staff') {
            if (\App\Models\Employee::where('user_id', $user->id)->exists()) {
                $roleSlug = 'staff';
                $staffRole = \App\Models\Role::where('slug', 'staff')->first();
                if ($staffRole) {
                    $user->update(['role_id' => $staffRole->id]);
                }
            }
        }

        if ($request->account_type === 'staff' && $roleSlug !== 'staff') {
            Session::flash('login_error', 'This account is not registered as Staff.');
            return back()->withErrors(['account_type' => 'This account is not registered as Staff.'])->onlyInput('email');
        }

        if ($request->account_type === 'student' && $roleSlug !== 'student') {
            Session::flash('login_error', 'This account is not registered as Student.');
            return back()->withErrors(['account_type' => 'This account is not registered as Student.'])->onlyInput('email');
        }

        if ($request->account_type === 'institute' && in_array($roleSlug, ['staff', 'student'])) {
            Session::flash('login_error', 'This account is not an Institute admin. Please use Staff or Student login.');
            return back()->withErrors(['account_type' => 'This account is not an Institute admin.'])->onlyInput('email');
        }

        // Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Complete login
        $this->completeLogin($user, $roleSlug);

        if (in_array($roleSlug, ['student', 'staff'])) {
            return $this->redirectByRole($roleSlug);
        }

        $intendedUrl = session()->pull('url.intended');
        if ($intendedUrl) {
            return redirect()->to($intendedUrl);
        }

        return $this->redirectByRole($roleSlug);
    }

    public function completeLogin($user, $roleSlug)
    {
        $displayName = $user->name;

        if ($roleSlug === 'student') {
            $student = \App\Models\Student::where('user_id', $user->id)->first();
            if ($student) {
                $displayName = trim($student->first_name . ' ' . ($student->last_name ?? ''));
            }
        }

        Session::put('user_id', $user->id);
        Session::put('user_name', $displayName);
        Session::put('user_role', $user->role?->role_name ?? ucfirst($roleSlug ?? 'User'));
        Session::put('user_role_slug', strtolower($roleSlug ?? 'user'));
    }

    public function redirectByRole($roleSlug)
    {
        $roleSlug = strtolower($roleSlug ?? '');
        if ($roleSlug === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($roleSlug === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $roleSlug = session('user_role_slug');
        
        Session::flush();
        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($roleSlug === 'student') {
            return redirect()->route('login.student')->with('success', 'You have been logged out successfully.');
        } elseif ($roleSlug === 'staff') {
            return redirect()->route('login.staff')->with('success', 'You have been logged out successfully.');
        } elseif ($roleSlug === 'admin' || $roleSlug === 'superadmin') {
            return redirect()->route('login.admin')->with('success', 'You have been logged out successfully.');
        }

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function updateProfilePic(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(session('user_id'));
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $filename);

            // Delete old file if it exists and isn't the default
            if ($user->profile_pic && $user->profile_pic !== 'default.png' && file_exists(public_path('uploads/profiles/' . $user->profile_pic))) {
                unlink(public_path('uploads/profiles/' . $user->profile_pic));
            }

            $user->profile_pic = $filename;
            $user->save();
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }
}
