<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthOrGuestAdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array($request->path(), ['/'])) {
            return redirect('/dashboard');
        }
        if (!Auth::check() && !$request->is('/')) {
            return redirect('/');
        }
        return $next($request);
    }


    
}
