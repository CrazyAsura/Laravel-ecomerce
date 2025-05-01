<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringMaintenance
{
    protected $except = [
        'api/v1/auth/login',
        'api/v1/auth/register',
        'api/v1/auth/logout',
        'api/v1/auth/refresh',
        'api/v1/auth/me',
        'api/v1/auth/verify-email',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
