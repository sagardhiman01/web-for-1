<?php

namespace App\Http\Middleware;

use Closure;

class ReferralMiddleware
{
    public function handle($request, Closure $next)
    {
        $currentReference = session()->get('reference');
        if ($currentReference && !preg_match('/^\d{6}$/', (string) $currentReference)) {
            session()->forget('reference');
        }

        $reference = trim((string) ($request->get('ref') ?? $request->get('reference') ?? $request->get('refcode') ?? $request->get('referral')));

        if (preg_match('/^\d{6}$/', $reference)) {
            session()->put('reference', $reference);
        }

        return $next($request);
    }
}
