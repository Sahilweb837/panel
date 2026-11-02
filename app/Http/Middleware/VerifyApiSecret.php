<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiSecret
{
    /**
     * Handle an incoming request and ensure it has a valid API secret or Sanctum token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated via Sanctum
        if ($request->user()) {
            return $next($request);
        }

        // 2. Check X-API-SECRET header or X-LMS-SECRET header
        $providedSecret = $request->header('X-API-SECRET')
            ?? $request->header('X-LMS-SECRET')
            ?? $request->header('x-api-secret')
            ?? $request->header('x-lms-secret')
            ?? $request->input('api_secret');

        $configuredSecret = env('LMS_API_SECRET', env('API_SECRET', 'netcoder_lms_sync_token_2026'));

        if (!empty($providedSecret) && hash_equals((string)$configuredSecret, (string)$providedSecret)) {
            return $next($request);
        }

        // 3. Check Bearer token against the secret directly
        $bearerToken = $request->bearerToken();
        if (!empty($bearerToken) && hash_equals((string)$configuredSecret, (string)$bearerToken)) {
            return $next($request);
        }

        // 4. Also check Sanctum personal access token if present
        if (!empty($bearerToken)) {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);
            if ($tokenModel && $tokenModel->tokenable) {
                // Authenticate the user for this request
                auth()->setUser($tokenModel->tokenable);
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: Invalid or missing API secret key or bearer token.',
            'code' => 401
        ], 401);
    }
}
