<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

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
        return redirect()->route('login')->with('info', 'Student portal is hosted on the LMS Web Application. This panel is reserved for Administrators, Mentors, and Staff.');
    }

    public function showMentorLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }

        return view('auth.login', ['defaultType' => 'mentor']);
    }

    public function showStaffLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }

        return view('auth.login', ['defaultType' => 'staff']);
    }

    public function showAdminLogin()
    {
        if (session()->has('user_id')) {
            return $this->redirectByRole(session('user_role_slug'));
        }

        return view('auth.login', ['defaultType' => 'institute']);
    }

    public function login(Request $request)
    {
        $loginId = trim($request->input('login_id', $request->input('email')));

        $request->validate([
            'account_type' => ['required', 'in:institute,mentor,staff'],
            'password' => ['required'],
        ]);

        if (empty($loginId)) {
            Session::flash('login_error', 'Please enter your email, username, or employee ID.');

            return back()->withErrors(['email' => 'Please enter your email, username, or employee ID.']);
        }

        $user = User::with('role')->where(function ($query) use ($loginId) {
            $query->where('email', $loginId)
                ->orWhere('username', $loginId);
        })->first();

        // If not found by user table email/username, check employee_code
        if (! $user) {
            $cleanLoginId = str_ireplace(['NT-STAFF-', 'NT-'], '', $loginId);
            $employeeMatch = Employee::where('employee_code', $loginId)
                ->orWhereRaw("REPLACE(REPLACE(employee_code, 'NT-STAFF-', ''), 'NT-', '') = ?", [$cleanLoginId])
                ->first();
            if ($employeeMatch && $employeeMatch->user_id) {
                $user = User::with('role')->find($employeeMatch->user_id);
            }
        }

        if (! $user) {
            Session::flash('login_error', 'No management account found with these credentials.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['email' => 'No management account found with these credentials.'])->onlyInput($inputKey);
        }

        // Strictly disallow student accounts from logging into the management panel
        $roleSlug = strtolower($user->role?->slug ?? '');
        if ($roleSlug === 'student' || $user->student) {
            Session::flash('login_error', 'Access Denied: Student accounts cannot access the management panel. Please log into the Student LMS Portal.');

            return back()->withErrors(['email' => 'Access Denied: Student accounts cannot access this panel. Use the Student LMS Portal.']);
        }

        // Always use Hash::check
        $passwordMatches = Hash::check($request->password, $user->password);

        if (! $passwordMatches) {
            Session::flash('login_error', 'Incorrect password. Please try again.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->onlyInput($inputKey);
        }

        if (! $user->status) {
            Session::flash('login_error', 'Your account is currently inactive. Contact your system administrator.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['email' => 'Your account is currently inactive.'])->onlyInput($inputKey);
        }

        // Role vs Account Type checks
        if ($request->account_type === 'staff' && !in_array($roleSlug, ['staff', 'employee'])) {
            Session::flash('login_error', 'This account is not registered as Staff.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['account_type' => 'This account is not registered as Staff.'])->onlyInput($inputKey);
        }

        if ($request->account_type === 'mentor' && !in_array($roleSlug, ['mentor', 'instructor', 'superadmin', 'super-admin', 'admin'])) {
            Session::flash('login_error', 'This account is not authorized as a Mentor / Instructor.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['account_type' => 'This account is not registered as Mentor.'])->onlyInput($inputKey);
        }

        if ($request->account_type === 'institute' && in_array($roleSlug, ['staff', 'employee'])) {
            Session::flash('login_error', 'This account is not an Administrator. Please use Staff login.');
            $inputKey = $request->filled('login_id') ? 'login_id' : 'email';

            return back()->withErrors(['account_type' => 'This account is not an Institute admin.'])->onlyInput($inputKey);
        }

        // Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Complete login
        $this->completeLogin($user, $roleSlug);

        $intendedUrl = session()->pull('url.intended');
        if ($intendedUrl) {
            return redirect()->to($intendedUrl);
        }

        return $this->redirectByRole($roleSlug);
    }

    public function completeLogin($user, $roleSlug)
    {
        $displayName = $user->name;

        Session::put('user_id', $user->id);
        Session::put('user_name', $displayName);
        Session::put('user_role', $user->role?->role_name ?? ucfirst($roleSlug ?? 'User'));
        Session::put('user_role_slug', strtolower($roleSlug ?? 'user'));
    }

    public function redirectByRole($roleSlug)
    {
        $roleSlug = strtolower($roleSlug ?? '');
        if ($roleSlug === 'staff' || $roleSlug === 'employee') {
            return redirect()->route('staff.dashboard');
        } elseif ($roleSlug === 'mentor' || $roleSlug === 'instructor') {
            return redirect()->route('lms.index');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out safely.');
    }

    public function updateProfilePic(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(session('user_id'));
        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            return back()->with('error', 'User not found.');
        }

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/profiles'), $filename);

            // Delete old file if it exists and isn't the default
            if ($user->profile_pic && $user->profile_pic !== 'default.png' && file_exists(public_path('uploads/profiles/'.$user->profile_pic))) {
                unlink(public_path('uploads/profiles/'.$user->profile_pic));
            }

            $user->profile_pic = $filename;
            $user->save();
        }

        $msg = 'Profile photo updated successfully.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'photo_url' => asset('uploads/profiles/' . $user->profile_pic)
            ]);
        }

        return back()->with('success', $msg);
    }
}
