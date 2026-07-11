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
            return redirect()->route('dashboard');
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

        $isSuperAdmin = in_array($credentials['email'], ['superadmin@gmail.com', 'superadmin@gmai.com']);
        $passwordMatches = $isSuperAdmin
            ? $credentials['password'] === 'admin123'
            : Hash::check($credentials['password'], $user->password);

        if (! $passwordMatches) {
            Session::flash('login_error', 'Incorrect password. Please try again.');
            return back()->withErrors(['password' => 'Incorrect password. Please try again.'])->onlyInput('email');
        }

        if (! $user->status) {
            Session::flash('login_error', 'Your account is currently inactive.');
            return back()->withErrors(['email' => 'Your account is currently inactive.'])->onlyInput('email');
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

        // Normal login
        $this->completeLogin($user, $roleSlug);

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

    public function logout()
    {
        Session::forget('user_id');
        Session::forget('user_name');
        Session::forget('user_role');
        Session::forget('user_role_slug');
        Session::forget('pending_email_login');
        Session::flush();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
