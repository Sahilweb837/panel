<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            return back()->withErrors(['email' => 'Invalid login credentials.'])->onlyInput('email');
        }

        // HARDCODED BYPASS FOR SUPERADMIN
        $isSuperAdmin = in_array($credentials['email'], ['superadmin@gmail.com', 'superadmin@gmai.com']);
        $passwordMatches = $isSuperAdmin 
            ? $credentials['password'] === 'admin123'
            : Hash::check($credentials['password'], $user->password);

        if (! $passwordMatches) {
            return back()->withErrors(['email' => 'Invalid login credentials.'])->onlyInput('email');
        }

        if (! $user->status) {
            return back()->withErrors(['email' => 'Your account is inactive.'])->onlyInput('email');
        }

        $roleSlug = $user->role?->slug;

        if ($credentials['account_type'] === 'staff' && $roleSlug !== 'staff') {
            return back()->withErrors(['account_type' => 'Please use Institute or Student login for this account.'])->onlyInput('email');
        }

        if ($credentials['account_type'] === 'student' && $roleSlug !== 'student') {
            return back()->withErrors(['account_type' => 'Please use Student login for this account.'])->onlyInput('email');
        }

        if ($credentials['account_type'] === 'institute' && in_array($roleSlug, ['staff', 'student'])) {
            return back()->withErrors(['account_type' => 'Please use Staff or Student login for this account.'])->onlyInput('email');
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role?->role_name ?? 'User',
            'user_role_slug' => $roleSlug ?? 'user',
        ]);

        $intendedUrl = session()->pull('url.intended');
        if ($intendedUrl) {
            return redirect()->to($intendedUrl);
        }

        if ($roleSlug === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($roleSlug === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->flush();

        return redirect()->route('login');
    }
}
