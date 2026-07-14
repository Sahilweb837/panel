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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'account_type' => ['required', 'in:institute,staff,student'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::with('role')->where('email', $credentials['email'])->first();

        if (! $user) {
            Session::flash('login_error', 'No account found with this email address.');
            return back()->withErrors(['email' => 'No account found with this email address.'])->onlyInput('email');
        }

        // Always use Hash::check – superadmin password must be stored as a bcrypt hash
        $passwordMatches = Hash::check($credentials['password'], $user->password);

        if (! $passwordMatches) {
            Session::flash('login_error', 'Incorrect password. Please try again.');
            return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->onlyInput('email');
        }

        if (! $user->status) {
            Session::flash('login_error', 'Your account is currently inactive.');
            return back()->withErrors(['email' => 'Your account is currently inactive.'])->onlyInput('email');
        }

        // Enforce Email Verification (skip for dummy emails)
        if (
            is_null($user->email_verified_at) &&
            !str_ends_with($user->email, '@student.com') &&
            !str_ends_with($user->email, '@staff.com')
        ) {
            Session::flash('login_error', 'Please verify your email address to log in. Check your inbox.');
            return back()->withErrors(['email' => 'Please verify your email address to log in. Check your inbox.'])->onlyInput('email');
        }

        $roleSlug = $user->role?->slug;

        if ($credentials['account_type'] === 'staff' && $roleSlug !== 'staff') {
            Session::flash('login_error', 'This account is not registered as Staff.');
            return back()->withErrors(['account_type' => 'This account is not registered as Staff.'])->onlyInput('email');
        }

        if ($credentials['account_type'] === 'student' && $roleSlug !== 'student') {
            Session::flash('login_error', 'This account is not registered as Student.');
            return back()->withErrors(['account_type' => 'This account is not registered as Student.'])->onlyInput('email');
        }

        if ($credentials['account_type'] === 'institute' && in_array($roleSlug, ['staff', 'student'])) {
            Session::flash('login_error', 'This account is not an Institute admin. Please use Staff or Student login.');
            return back()->withErrors(['account_type' => 'This account is not an Institute admin.'])->onlyInput('email');
        }

        // Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Complete login
        $this->completeLogin($user, $roleSlug);

        // Always redirect by role – never use url.intended for students/staff to avoid
        // them being sent to admin pages they may have tried to access before.
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
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->name);
        Session::put('user_role', $user->role?->role_name ?? 'User');
        Session::put('user_role_slug', $roleSlug ?? 'user');
        Session::forget('pending_email_login');
    }

    public function redirectByRole($roleSlug)
    {
        if ($roleSlug === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($roleSlug === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Session::flush();
        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
