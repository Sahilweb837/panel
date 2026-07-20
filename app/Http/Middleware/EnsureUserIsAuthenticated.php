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

        // ----------------------------------------------------------------
        // STUDENT: can ONLY access student.* routes + logout
        // ----------------------------------------------------------------
        if ($roleSlug === 'student') {
            $route = $request->route() ? $request->route()->getName() : '';
            if ($request->routeIs('logout') || str_starts_with($route, 'student.') || str_starts_with($route, 'messages.')) {
                return $next($request);
            }
            // Block everything else – send back to student portal
            return redirect()->route('student.dashboard')
                ->with('error', 'Access denied. Students can only access the student portal.');
        }

        // ----------------------------------------------------------------
        // STAFF: can ONLY access staff.* routes + logout + messages.*
        // ----------------------------------------------------------------
        if ($roleSlug === 'staff') {
            $route = $request->route() ? $request->route()->getName() : '';
            if ($request->routeIs('logout') || str_starts_with($route, 'staff.') || str_starts_with($route, 'messages.')) {
                return $next($request);
            }
            // Block everything else – send back to staff portal
            return redirect()->route('staff.dashboard')
                ->with('error', 'Access denied. Staff can only access the staff portal.');
        }

        // ----------------------------------------------------------------
        // SUPER ADMIN: unrestricted access
        // ----------------------------------------------------------------
        if ($isSuperOrRoot) {
            return $next($request);
        }

        // ----------------------------------------------------------------
        // SUB-ADMIN: access determined by the 'access' array on the user
        // ----------------------------------------------------------------
        $route = $request->route() ? $request->route()->getName() : '';
        $access = $user->access ?? [];

        $allowed = false;

        if ($request->routeIs('dashboard', 'logout', 'clear-cache')) {
            $allowed = true;
        } elseif (str_starts_with($route, 'courses.') && in_array('courses', $access)) {
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
        } elseif (str_starts_with($route, 'clients.') && in_array('clients', $access)) {
            $allowed = true;
        } elseif (str_starts_with($route, 'client_invoices.') && in_array('clients', $access)) {
            $allowed = true;
        } elseif (str_starts_with($route, 'reports.') && in_array('reports', $access)) {
            $allowed = true;
        } elseif (str_starts_with($route, 'tasks.') && in_array('tasks', $access)) {
            $allowed = true;
        } elseif (str_starts_with($route, 'daily-updates.') && in_array('daily-updates', $access)) {
            $allowed = true;
        } elseif (str_starts_with($route, 'prospects.') && in_array('prospects', $access)) {
            $allowed = true;
        }

        if (!$allowed) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this module.');
        }

        $response = $next($request);
        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
