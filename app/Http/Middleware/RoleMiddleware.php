<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower(Auth::user()->role); // lowercase for consistency
        $roles = array_map('strtolower', $roles);    // lowercase roles passed to middleware

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access'); // optional message
        }

        return $next($request);
    }
}
