<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class AdminUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/login') && !Auth::check()) {
            return $next($request);
        }

        if (!Auth::check() || Auth::user()->role !== 'admin') {

            Auth::logout();
 
            $request->session()->invalidate();
 
            $request->session()->regenerateToken();
 
            return redirect('/')->with('error','Admin only!');
        }
        return $next($request);
    }
}