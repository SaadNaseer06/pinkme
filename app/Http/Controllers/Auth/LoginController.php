<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Your Blade view
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // Allow login by email or phone
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$fieldType => $request->login, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();

            // Determine the redirect URL based on role
            $roleName = Auth::user()->role->name;

            switch ($roleName) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'sponsor':
                    return redirect()->route('sponsor.dashboard');
                case 'casemanager':
                    return redirect()->route('case_manager.dashboard');
                case 'patient':
                    return redirect()->route('patient.dashboard');
                default:
                    // fallback if role unknown
                    return redirect('/');
            }
        }

        return redirect()->route('register', ['tab' => 'login'])->withErrors([
            'login' => 'These credentials do not match our records.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('register', ['tab' => 'login']);
    }
}
