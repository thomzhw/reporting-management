<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Determine redirect URL based on role
            $redirectUrl = match($user->role->name) {
                'superuser' => route('superuser.dashboard'),
                'head' => route('head.dashboard'),
                default => route('staff.dashboard')
            };
            
            // For AJAX requests (your form uses myForm class)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'successRedirect',
                    'message' => 'Login successful!',
                    'redirect' => $redirectUrl
                ]);
            }
            
            // For regular form submissions
            return redirect($redirectUrl);
        }

        // Login failed
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid credentials'
            ], 401);
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/signin');
    }
}
