<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    public function redirectTo()
    {
        // Memberikan hint ke Intelephense bahwa ini adalah App\Models\User
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek role menggunakan fungsi dari Spatie
        if ($user->hasRole('admin')) {
            return '/dashboard-admin';
        } elseif ($user->hasRole('teacher')) {
            return '/dashboard-teacher';
        } elseif ($user->hasRole('student')) {
            return '/dashboard-student';
        }

        // Default redirect jika user tidak punya role di atas
        return '/home'; 
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }
}
