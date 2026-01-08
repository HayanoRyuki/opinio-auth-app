<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Internal-Token');

        if (! $token || $token !== config('services.internal.token')) {
            abort(401, 'Unauthorized internal request');
        }

        return $next($request);
    }
}
