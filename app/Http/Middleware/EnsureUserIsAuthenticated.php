<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('user_id')) {
            $request->session()->put('url.intended', $request->fullUrl());
            return redirect()->route('login');
        }

        $user = User::with('role')->find($request->session()->get('user_id'));

        if (! $user) {
            $request->session()->flush();
            return redirect()->route('login');
        }

        view()->share('currentUser', $user);

        $roleSlug = $user->role?->slug;
        $isSuperOrRoot = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin']);

        if (!$isSuperOrRoot) {
            $allowed = false;
            
            $route = $request->route() ? $request->route()->getName() : '';

            if ($request->routeIs('dashboard', 'logout')) {
                $allowed = true;
            } elseif ($roleSlug === 'student' && str_starts_with($route, 'student.')) {
                $allowed = true;
            } elseif ($roleSlug === 'staff' && str_starts_with($route, 'staff.')) {
                $allowed = true;
            } else {
                $access = $user->access ?? [];
                
                if (str_starts_with($route, 'courses.') && in_array('courses', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'students.') && in_array('students', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'employees.') && in_array('employees', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'employee-attendances.') && in_array('employee-attendances', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'attendances.') && in_array('attendances', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'biometric.') && in_array('attendances', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'fee_invoices.') && in_array('fee-invoices', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'expenses.') && in_array('expenses', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'salary_slips.') && in_array('salary-slips', $access)) {
                    $allowed = true;
                } elseif (str_starts_with($route, 'trainings.') && in_array('trainings', $access)) {
                    $allowed = true;
                }
            }

            if (!$allowed) {
                return redirect()->route('dashboard')->with('error', 'You do not have permission to access this module.');
            }
        }

        return $next($request);
    }
}
