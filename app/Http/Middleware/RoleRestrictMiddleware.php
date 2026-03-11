<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRestrictMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('register', ['tab' => 'login']));
        }
        $role = $user->role->name;
        $routePrefix = ltrim($request->route()->getPrefix(), '/');

        switch ($role) {
            case 'admin':
                if ($routePrefix !== 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                break;
            // case 'sponsor': sponsor user not important for now - falls through to default
            //     if ($routePrefix !== 'sponsor') {
            //         return redirect()->route('sponsor.dashboard');
            //     }
            //     break;
            case 'casemanager':
                if ($routePrefix !== 'case_manager') {
                    return redirect()->route('case_manager.dashboard');
                }
                break;
            case 'patient':
                if ($routePrefix !== 'patient') {
                    return redirect()->route('patient.dashboard');
                }
                break;
            case 'finance':
                if ($routePrefix !== 'finance') {
                    return redirect()->route('finance.dashboard');
                }
                break;
            default:
                Auth::logout();
                return redirect()->guest(route('register', ['tab' => 'login']));
        }

        return $next($request);
    }
}
