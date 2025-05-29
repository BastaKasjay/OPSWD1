<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MunicipalityController;


// Home page
Route::get('/', function () {
    return view('HomePage.home');
})->name('home');

// Login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login submit (dummy for now)
Route::post('/login', function () {
    // Authentication logic goes here
    return redirect()->route('home');
})->name('login.submit');

Route::get('/client', [ClientController::class, 'index'])->name('client.index');
Route::get('/client/create', [ClientController::class, 'create'])->name('client.create');

Route::post('/client', [ClientController::class, 'docs'])->name('client.docs');




// List all municipalities
Route::get('/municipalities', [MunicipalityController::class, 'index'])->name('municipalities.index');

// Edit a municipality
Route::get('/municipalities/{id}/edit', [MunicipalityController::class, 'edit'])->name('municipalities.edit');