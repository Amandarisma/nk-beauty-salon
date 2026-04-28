<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
public function handle(Request $request, Closure $next)
{
    // Jika user terautentikasi dan rolenya 'admin', persilakan masuk
    if (!Auth::check()) {
        return redirect('/login');
    }
 //Jika bukan admin, tendang ke error 403
    if (Auth::user()->role !== 'admin') {
        abort(403, 'Akses hanya untuk admin.');
    }

    return $next($request);
}
}
