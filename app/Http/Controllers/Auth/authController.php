<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\changePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    protected function UpdatePass(changePasswordRequest $request)
    {
        $user = Auth::user();

        try {
            // Check if current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'error update');
        }
        return redirect()->back()->with('success', 'success update');
        
    }
}
