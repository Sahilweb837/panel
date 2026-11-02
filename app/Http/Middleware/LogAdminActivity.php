<?php

namespace App\Http\Middleware;

use App\Models\AdminActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    /**
     * Keys to mask in logged request data.
     */
    protected array $hiddenKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'raw_password',
        'token',
        'api_secret',
        'secret',
        'credit_card',
    ];

    /**
     * Handle an incoming request and log mutations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log state-changing requests or login attempts
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            try {
                $payload = $request->except($this->hiddenKeys);

                $action = $request->route() ? ($request->route()->getName() ?? $request->path()) : $request->path();

                AdminActivityLog::create([
                    'user_id' => session('user_id') ?? ($request->user() ? $request->user()->id : null),
                    'user_name' => session('user_name') ?? ($request->user() ? $request->user()->name : 'Guest'),
                    'user_role' => session('user_role_slug') ?? ($request->user() && $request->user()->role ? $request->user()->role->slug : 'guest'),
                    'action' => $action,
                    'method' => $request->method(),
                    'route' => $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                    'request_data' => !empty($payload) ? $payload : null,
                    'status_code' => $response->getStatusCode(),
                ]);
            } catch (\Throwable $e) {
                // Fail silently so logging never breaks application flow
            }
        }

        return $response;
    }
}
