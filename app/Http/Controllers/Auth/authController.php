<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\changePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    protected function UpdateProfile(changePasswordRequest $request)
    {
        $request->validated();

        try {
            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $data = [];

            if ($request->filled('username')) {
                $data['username'] = $request->username;
            }

            if ($request->filled('new_password')) {
                $data['password'] = Hash::make($request->new_password);
            }

            if (!empty($data)) {
                $user->update($data);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Error updating profile.');
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user->hasRole('teacher')) {
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'user' => $user->teacher
            ]);
        } else if ($user->hasRole('student')) {
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return response()->json([
                'user' => $user->student
            ]);
        } else {
            return response()->json([
                'messages' => 'Sign In failed'
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
