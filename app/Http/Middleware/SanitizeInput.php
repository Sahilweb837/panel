<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Input keys that should not have HTML tags stripped (e.g. rich text / passwords).
     *
     * @var array<int, string>
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'raw_password',
        'main_content',
        'content',
        'section_content',
        'bio',
        'notes',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if (!empty($input)) {
            array_walk_recursive($input, function (&$value, $key) {
                if (is_string($value)) {
                    // Always remove null byte characters to prevent poison null byte attacks
                    $value = str_replace(chr(0), '', $value);

                    // If not an exempted field, strip dangerous tags and script tokens
                    if (!in_array($key, $this->except, true)) {
                        // Protect against script tags and event handlers
                        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);
                        $value = preg_replace('/javascript\s*:/i', '', $value);
                        $value = strip_tags($value);
                    }
                }
            });

            $request->merge($input);
        }

        return $next($request);
    }
}
