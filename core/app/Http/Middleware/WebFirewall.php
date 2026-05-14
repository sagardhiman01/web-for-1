<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebFirewall
{
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

        // Check for common malicious patterns in request data
        array_walk_recursive($input, function ($value) {
            if (is_string($value)) {
                $patterns = [
                    // SQL Injection patterns
                    '/(?:union\s+all\s+select|union\s+select)/i',
                    '/(?:information_schema|mysql\.user)/i',
                    '/\/\*.*?\*\//s', // Block multi-line SQL comments only
                    // XSS patterns
                    '/<script\b[^>]*>(.*?)<\/script>/i',
                    '/<img\b[^>]*src=[\"\'\s]?javascript:/i',
                    '/(?:onmouseover|onerror|onload|onfocus|onclick)\s*=/i',
                    // LFI/RFI patterns
                    '/(?:\.\.\/|\.\.\\\\|\/etc\/passwd)/i',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        abort(403, 'Access Denied: Malicious activity detected. Your IP has been logged.');
                    }
                }
            }
        });

        $response = $next($request);

        // Remove server headers to hide identity
        if (method_exists($response, 'header')) {
            $response->header('X-Powered-By', '');
            $response->header('Server', '');
            // Add security headers
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }
}
