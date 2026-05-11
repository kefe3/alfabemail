<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DevelopmentAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isProduction()) {
            return $next($request);
        }

        return $next($request);
    }
}