<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Client;

use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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
use Illuminate\Support\Facades\Auth;

// Home page (login protected)
Route::get('/home', function () {
    return view('HomePage.home');
})->name('home');



// Redirect root URL to home page
Route::get('/', function () {
    return redirect()->route('login');
});

// claims
Route::get('/claims/grouped', [ClaimController::class, 'groupedClaims'])->name('claims.grouped');
Route::post('/claims/update-status/{clientId}', [ClaimController::class, 'updateStatus'])->name('claims.updateStatus');


// assistance
Route::get('/clients/assistance', [ClientController::class, 'assistancesView'])->name('clients.assistance');

// Route::patch('/claims/{id}/update-status', function() { dd('teee')})->name('claims.update-status');
Route::patch('/claims/{id}/update-status', [ClaimController::class, 'updateStatus'])->name('claims.update-status');
// Route for updating claim record directly
Route::put('/claims/{id}', [ClaimController::class, 'update'])->name('claims.update');

// Client routes
Route::resource('clients', ClientController::class);


// Disbursement routes
Route::patch('/disbursements/{id}/update-claim-status', [DisbursementController::class, 'updateClaimStatus'])->name('disbursements.updateClaimStatus');



// Municipality routes
Route::resource('municipalities', MunicipalityController::class)->only(['index', 'edit', 'update']);

// Dynamic dropdowns
Route::get('/get-requirements/{id}', [AssistanceController::class, 'getRequirements']);
Route::get('/get-categories/{id}', [AssistanceController::class, 'getCategories']);

//Reports rooutes
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// Other resource routes
Route::resource('payees', PayeeController::class);
Route::resource('requirements', RequirementController::class);
Route::resource('assistance-categories', AssistanceCategoryController::class);
Route::resource('vulnerability-sectors', VulnerabilitySectorController::class);
Route::resource('client-assistances', ClientAssistanceController::class);
Route::resource('assistance-types', AssistanceController::class);
Route::resource('employees', EmployeeController::class);
Route::resource('roles', RoleController::class);

// User Management Routes (now public)
Route::resource('users', UserController::class);

// Employee Management Routes
Route::resource('employees', EmployeeController::class);



// Custom Login submit
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




// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');


// add assistance search bar
Route::get('/api/search-clients', function (Request $request) {
    $query = $request->get('q');

    $clients = Client::where('first_name', 'LIKE', "%{$query}%")
        ->orWhere('middle_name', 'LIKE', "%{$query}%")
        ->orWhere('last_name', 'LIKE', "%{$query}%")
        ->limit(10)
        ->get()
        ->map(function ($client) {
            return [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
            ];
        });

    return response()->json($clients);
});