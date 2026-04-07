<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Paths / route prefixes where guests should use staff login (email + password), not patient register.
     *
     * @var list<string>
     */
    private const STAFF_PORTAL_PREFIXES = ['admin', 'case_manager', 'finance'];

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        $prefix = ltrim((string) $request->route()?->getPrefix(), '/');

        if (in_array($prefix, self::STAFF_PORTAL_PREFIXES, true)) {
            return route('login.staff');
        }

        return route('register', ['tab' => 'login']);
    }
}
