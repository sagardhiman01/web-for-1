<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    /**
     * Redirect HTTP traffic to HTTPS when enabled.
     * This is intentionally env-driven so local/dev setups stay unaffected.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldForceHttps($request)) {
            $query = $request->getQueryString();
            $target = 'https://' . $request->getHttpHost() . $request->getPathInfo() . ($query ? '?' . $query : '');

            return redirect()->to($target, 301);
        }

        return $next($request);
    }

    protected function shouldForceHttps(Request $request): bool
    {
        $forceHttps = filter_var(env('FORCE_HTTPS', app()->environment('production')), FILTER_VALIDATE_BOOL);

        if (!$forceHttps) {
            return false;
        }

        if (app()->environment(['local', 'testing'])) {
            return false;
        }

        if ($request->secure()) {
            return false;
        }

        if (strtolower((string) $request->header('X-Forwarded-Proto')) === 'https') {
            return false;
        }

        return true;
    }
}
