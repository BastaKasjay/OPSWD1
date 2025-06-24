<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ClientController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\ClientAssistanceController;
use App\Http\Controllers\PayeeController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\AssistanceCategoryController;
use App\Http\Controllers\VulnerabilitySectorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;

// Home page (login protected)
Route::get('/home', function () {
    if (!session('is_admin')) {
        return redirect()->route('login');
    }
    return view('HomePage.home');
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
    $request->session()->flush();
    return redirect()->route('login');
})->name('logout');

// Client routes
Route::resource('clients', ClientController::class);


// Municipality routes (optional edit only)
Route::resource('municipalities', MunicipalityController::class)->only(['index', 'edit', 'update']);

// Dynamic loading routes for dependent dropdowns (Assistance Type)
Route::get('/get-requirements/{id}', [AssistanceController::class, 'getRequirements']);
Route::get('/get-categories/{id}', [AssistanceController::class, 'getCategories']);

// Full resource routes for other models
Route::resource('payees', PayeeController::class);
Route::resource('requirements', RequirementController::class);
Route::resource('assistance-categories', AssistanceCategoryController::class);
Route::resource('vulnerability-sectors', VulnerabilitySectorController::class);
Route::resource('client-assistances', ClientAssistanceController::class);
Route::resource('assistance-types', AssistanceController::class);
Route::resource('employees', EmployeeController::class);
Route::resource('roles', RoleController::class);
