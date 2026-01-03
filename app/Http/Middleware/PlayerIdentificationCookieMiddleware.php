<?php

namespace App\Http\Middleware;

use App\Support\PlayerCookie;
use Closure;
use Illuminate\Http\Request;

class PlayerIdentificationCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        PlayerCookie::getOrCreate();

        return $next($request);
    }
}
