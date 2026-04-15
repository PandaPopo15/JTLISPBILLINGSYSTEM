<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureInstaller
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->is_admin !== 2) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
