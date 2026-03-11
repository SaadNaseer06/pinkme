<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.signup_login', [
            'initialTab' => 'login',
            'rememberedLogin' => Cookie::get('remembered_login'),
            'rememberedPassword' => Cookie::get('remembered_password'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        // Allow login by email or phone
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt(
            [$fieldType => $request->login, 'password' => $request->password],
            $request->filled('remember')
        )) {
            $request->session()->regenerate();

            if ($request->filled('remember')) {
                Cookie::queue('remembered_login', $request->login, 60 * 24 * 30);
                Cookie::queue('remembered_password', $request->password, 60 * 24 * 30);
            } else {
                Cookie::queue(Cookie::forget('remembered_login'));
                Cookie::queue(Cookie::forget('remembered_password'));
            }

            // Redirect to intended page (e.g. page user was trying to access before login), or role-based dashboard
            $roleName = Auth::user()->role->name;
            $defaultUrl = match ($roleName) {
                'admin' => route('admin.dashboard'),
                'casemanager' => route('case_manager.dashboard'),
                'patient' => route('patient.dashboard'),
                'finance' => route('finance.dashboard'),
                default => url('/'),
            };

            return redirect()->intended($defaultUrl);
        }

        if (! $request->filled('remember')) {
            Cookie::queue(Cookie::forget('remembered_login'));
        }

        Cookie::queue(Cookie::forget('remembered_password'));

        $loginValue = $request->input('login', '');
        return redirect()->route('register', ['tab' => 'login'])
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'These credentials do not match our records for "' . e($loginValue) . '".',
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
