<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /** Roles allowed to use the dedicated staff login page (email + password). */
    private const STAFF_ROLES = ['admin', 'casemanager', 'finance'];

    private const COOKIE_STAFF_EMAIL = 'staff_remembered_email';

    private const COOKIE_STAFF_PASSWORD = 'staff_remembered_password';

    private const STAFF_PORTALS = [
        'admin' => [
            'heading' => 'Admin sign in',
            'expected_role' => 'admin',
        ],
        'coordinator' => [
            'heading' => 'Patient Coordinator sign in',
            'expected_role' => 'casemanager',
        ],
        'finance' => [
            'heading' => 'Finance & Grant Manager sign in',
            'expected_role' => 'finance',
        ],
    ];

    public function showStaffLoginForm(Request $request)
    {
        $portal = strtolower((string) $request->query('portal', ''));
        if ($portal === '' && ! $request->boolean('finance')) {
            return view('auth.staff_portal');
        }

        if ($request->boolean('finance') && $portal === '') {
            $portal = 'finance';
        }

        $portalConfig = self::STAFF_PORTALS[$portal] ?? null;
        if ($portalConfig === null) {
            return view('auth.staff_portal');
        }

        return view('auth.staff_login', [
            'rememberedStaffEmail' => Cookie::get(self::COOKIE_STAFF_EMAIL),
            'rememberedStaffPassword' => Cookie::get(self::COOKIE_STAFF_PASSWORD),
            'staffLoginHeading' => $portalConfig['heading'],
            'staffAccessNotice' => Brand::staffAccessNotice(),
            'staffPortal' => $portal,
            'staffPortalBackUrl' => route('login.staff'),
        ]);
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

        $remember = $request->boolean('remember');

        if (! Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $remember
        )) {
            if (! $remember) {
                Cookie::queue(Cookie::forget(self::COOKIE_STAFF_EMAIL));
            }
            Cookie::queue(Cookie::forget(self::COOKIE_STAFF_PASSWORD));

            $redirectParams = array_filter([
                'portal' => $request->input('portal'),
            ]);

            return redirect()->route('login.staff', $redirectParams)
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ]);
        }

        $user = Auth::user();
        $roleName = $user->role?->name;
        $portal = strtolower((string) $request->input('portal', ''));
        $expectedRole = self::STAFF_PORTALS[$portal]['expected_role'] ?? null;

        if ($expectedRole !== null && $roleName !== $expectedRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if (! $remember) {
                Cookie::queue(Cookie::forget(self::COOKIE_STAFF_EMAIL));
            }
            Cookie::queue(Cookie::forget(self::COOKIE_STAFF_PASSWORD));

            $portalLabel = match ($expectedRole) {
                'admin' => 'Admin',
                'casemanager' => 'Patient Coordinator',
                'finance' => 'Finance & Grant Manager',
                default => 'staff',
            };

            return redirect()->route('login.staff', ['portal' => $portal ?: null])
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => __('This account cannot sign in through the :portal portal. Please choose the correct role or contact your administrator.', ['portal' => $portalLabel]),
                ]);
        }

        if (! in_array($roleName, self::STAFF_ROLES, true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if (! $remember) {
                Cookie::queue(Cookie::forget(self::COOKIE_STAFF_EMAIL));
            }
            Cookie::queue(Cookie::forget(self::COOKIE_STAFF_PASSWORD));

            return redirect()->route('login.staff')
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => __('This sign-in page is for staff only. Patients should use the main login.'),
                ]);
        }

        if ($remember) {
            Cookie::queue(self::COOKIE_STAFF_EMAIL, $request->email, 60 * 24 * 30);
            Cookie::queue(self::COOKIE_STAFF_PASSWORD, $request->password, 60 * 24 * 30);
        } else {
            Cookie::queue(Cookie::forget(self::COOKIE_STAFF_EMAIL));
            Cookie::queue(Cookie::forget(self::COOKIE_STAFF_PASSWORD));
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

        $remember = $request->boolean('remember');

        if (Auth::attempt(
            [$fieldType => $request->login, 'password' => $request->password],
            $remember
        )) {
            $request->session()->regenerate();

            if ($remember) {
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

        if (! $remember) {
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
