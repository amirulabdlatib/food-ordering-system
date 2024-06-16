<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class ManagerUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('manager/login') && !Auth::check()) {
            return $next($request);
        }

        if (!Auth::check() || Auth::user()->role !== 'manager') {

            Auth::logout();
 
            $request->session()->invalidate();
 
            $request->session()->regenerateToken();
 
            return redirect('/')->with('error','Manager only!');
        }
        return $next($request);
    }
}