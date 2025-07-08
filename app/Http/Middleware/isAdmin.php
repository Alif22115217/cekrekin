<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is an admin or superadmin
        if (Auth::user()->role == 1 || Auth::user()->role == 2) {
            return $next($request);  // Allow access if the user is admin or superadmin
        } else {
            return redirect(route('member.index'));  // Redirect to member dashboard if not admin
        }
    }
}