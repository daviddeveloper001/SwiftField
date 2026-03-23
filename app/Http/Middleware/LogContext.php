<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogContext
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            Log::shareContext([
                'user_id' => auth()->id(),
                'tenant_id' => auth()->user()->tenant_id,
                'url' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}