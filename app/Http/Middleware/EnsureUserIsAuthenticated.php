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
            return redirect()->route('login');
        }

        $user = User::with('role')->find($request->session()->get('user_id'));

        if (! $user) {
            $request->session()->flush();
            return redirect()->route('login');
        }

        view()->share('currentUser', $user);

        if ($user->role?->slug === 'staff' && ! $request->routeIs('dashboard', 'logout')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
