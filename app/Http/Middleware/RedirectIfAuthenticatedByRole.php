<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $roleName = $user->role->name; 

            switch ($roleName) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                // case 'sponsor': sponsor not important for now - falls through to default
                //     return redirect()->route('sponsor.dashboard');
                case 'casemanager':
                    return redirect()->route('case_manager.dashboard');
                case 'patient':
                    return redirect()->route('patient.dashboard');
                default:
                    // fallback if role not recognized
                    return redirect()->route('register', ['tab' => 'login']);
            }
        }

        return $next($request);
    }
}
