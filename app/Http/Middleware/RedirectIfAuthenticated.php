<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check()) {
        //         return redirect(RouteServiceProvider::HOME);
        //     }
        // }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard_admin');
                } elseif ($user->hasRole('student')) {
                    return redirect()->route('student.dashboard_student');
                } elseif ($user->hasRole('teacher')) {
                    return redirect()->route('teacher.dashboard_teacher');
                } elseif ($user->hasRole('studentShip')) {
                    return redirect()->route('studentShip.dashboard_studentShip');
                }
                // Add more roles as needed
            }
        }

        return $next($request);
    }
}
