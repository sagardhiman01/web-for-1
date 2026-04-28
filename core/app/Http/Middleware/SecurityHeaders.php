<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Apply common security headers without overriding existing app/server values.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!filter_var(env('SECURITY_HEADERS_ENABLED', true), FILTER_VALIDATE_BOOL)) {
            return $response;
        }

        $this->setIfMissing($response, 'X-Content-Type-Options', 'nosniff');
        $this->setIfMissing($response, 'X-Frame-Options', env('SECURITY_FRAME_OPTIONS', 'SAMEORIGIN'));
        $this->setIfMissing($response, 'Referrer-Policy', env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'));
        $this->setIfMissing($response, 'Permissions-Policy', env('SECURITY_PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=()'));

        $csp = trim((string) env('SECURITY_CONTENT_SECURITY_POLICY', ''));
        if ($csp !== '') {
            $this->setIfMissing($response, 'Content-Security-Policy', $csp);
        }

        if ($request->secure() && filter_var(env('SECURITY_HSTS_ENABLED', true), FILTER_VALIDATE_BOOL)) {
            $hstsMaxAge = (int) env('SECURITY_HSTS_MAX_AGE', 31536000);
            $this->setIfMissing($response, 'Strict-Transport-Security', 'max-age=' . $hstsMaxAge . '; includeSubDomains');
        }

        return $response;
    }

    protected function setIfMissing(Response $response, string $header, string $value): void
    {
        if ($value === '' || $response->headers->has($header)) {
            return;
        }

        $response->headers->set($header, $value);
    }
}

