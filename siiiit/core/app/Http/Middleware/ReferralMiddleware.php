<?php

namespace App\Http\Middleware;

use Closure;

class ReferralMiddleware
{
    public function handle($request, Closure $next)
    {
       
        if ($request->has('ref')) {
            session()->put('reference', $request->get('ref'));
        }

        return $next($request);
    }
}
