<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        logger("FortifyServiceProvider booted!");

        Fortify::loginView(fn () => view('auth.login'));

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('username', $request->username)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
            return null;
        });

        // ✅ Custom login redirect for older Fortify versions
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    // Force session regeneration with user ID
                    $request->session()->regenerate();
                    
                    return redirect('/home');
                }
            };
        });
    }
}
