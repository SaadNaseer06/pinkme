<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /** Roles allowed to use the dedicated staff login page (email + password). */
    private const STAFF_ROLES = ['admin', 'casemanager', 'finance'];

    public function showStaffLoginForm()
    {
        return view('auth.staff_login');
    }

    /**
     * Sign-in for admin, case managers, and finance users only.
     */
    public function staffLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            return redirect()->route('login.staff')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ]);
        }

        $user = Auth::user();
        $roleName = $user->role?->name;

        if (! in_array($roleName, self::STAFF_ROLES, true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.staff')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __('This sign-in page is for staff only. Patients should use the main login.'),
                ]);
        }

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        $defaultUrl = match ($roleName) {
            'admin' => route('admin.dashboard'),
            'casemanager' => route('case_manager.dashboard'),
            'finance' => route('finance.dashboard'),
            default => route('login.staff'),
        };

        return redirect()->to($defaultUrl);
    }

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

            // Always send users to their role dashboard after login.
            $roleName = Auth::user()->role->name;
            $defaultUrl = match ($roleName) {
                'admin' => route('admin.dashboard'),
                'casemanager' => route('case_manager.dashboard'),
                'patient' => route('patient.dashboard'),
                'finance' => route('finance.dashboard'),
                default => url('/'),
            };

            $request->session()->forget('url.intended');

            return redirect()->to($defaultUrl);
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
        $roleName = Auth::user()?->role?->name;
        $wasStaff = in_array($roleName, self::STAFF_ROLES, true);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $wasStaff
            ? redirect()->route('login.staff')
            : redirect()->route('register', ['tab' => 'login']);
    }
}
