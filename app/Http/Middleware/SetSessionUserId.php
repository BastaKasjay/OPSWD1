<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetSessionUserId
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // After the request, if user is authenticated and using database sessions
        if (Auth::check() && config('session.driver') === 'database') {
            $sessionId = $request->session()->getId();
            $userId = Auth::id();

            // Update the session record to include user_id
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update(['user_id' => $userId]);
        }

        return $response;
    }
}
