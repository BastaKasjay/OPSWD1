<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MunicipalityController;
use Illuminate\Http\Request;


// Home page

Route::get('/home', function () {
    // Optional: check if admin is logged in
    if (!session('is_admin')) {
        return redirect()->route('login');
    }

    return view('HomePage.home'); // or whatever your actual view is
})->name('home');

// Redirect root URL to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login submit route
Route::post('/login', function (Request $request) {
    $username = $request->input('username');
    $password = $request->input('password');

    // Hardcoded admin credentials (you can later move these to .env for security)
    if ($username === 'admin' && $password === 'admin123') {
        $request->session()->put('is_admin', true);
        return redirect()->route('home');
    }

    return back()->withErrors([
        'login' => 'Invalid username or password.',
    ]);
})->name('login.submit');

// Logout route
Route::post('/logout', function (Request $request) {
    $request->session()->flush(); // remove all session data
    return redirect()->route('login');
})->name('logout');

// Client routes
Route::get('/client', [ClientController::class, 'index'])->name('client.index');
Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');

Route::post('/client', [ClientController::class, 'docs'])->name('client.docs');




// List all municipalities
Route::get('/municipalities', [MunicipalityController::class, 'index'])->name('municipalities.index');

// Edit a municipality
Route::get('/municipalities/{id}/edit', [MunicipalityController::class, 'edit'])->name('municipalities.edit');