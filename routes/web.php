<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

use App\Models\Claim;
use App\Http\Controllers\DashboardController;
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

Route::get('/', function () {
    return redirect('/login');
});



Route::get('/login', fn() => view('auth.login'))->name('login');


Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->route('home');
    }

    return back()->withErrors(['username' => 'Invalid username or password.']);
})->name('login.post');


Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Protected routes

    Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::post('/budgets', [DashboardController::class, 'storeBudget'])->name('budgets.store');

Route::get('/claims/grouped', [ClaimController::class, 'groupedClaims'])->name('claims.grouped');
Route::patch('/claims/{id}/update-status', [ClaimController::class, 'updateStatus'])->name('claims.update-status');
Route::put('/claims/{id}', [ClaimController::class, 'update'])->name('claims.update');

Route::get('/clients/assistance', [ClientAssistanceController::class, 'assistance'])->name('clients.assistance');
Route::resource('clients', ClientController::class);
Route::match(['put', 'patch'], 'disbursements/{id}/update-claim-status', [DisbursementController::class, 'updateClaimStatus'])->name('disbursements.updateClaimStatus');
Route::get('/disbursements', [DisbursementController::class, 'index'])->name('disbursements.index');

Route::resource('municipalities', MunicipalityController::class)->only(['index', 'edit', 'update']);
Route::get('/get-requirements/{id}', [AssistanceController::class, 'getRequirements']);
Route::get('/get-categories/{id}', [AssistanceController::class, 'getCategories']);
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::resource('payees', PayeeController::class);
Route::resource('requirements', RequirementController::class);
Route::resource('assistance-categories', AssistanceCategoryController::class);
Route::resource('vulnerability-sectors', VulnerabilitySectorController::class);
Route::resource('client-assistances', ClientAssistanceController::class);
Route::resource('assistance-types', AssistanceController::class);
Route::resource('employees', EmployeeController::class);
Route::resource('roles', RoleController::class);
Route::resource('users', UserController::class);

Route::post('/disbursements/batch-update', [DisbursementController::class, 'batchUpdate'])->name('disbursements.batchUpdate');


Route::get('/api/search-clients', function (Request $request) {
    $query = $request->get('q');
    $clients = Client::where('first_name', 'LIKE', "%{$query}%")
        ->orWhere('middle_name', 'LIKE', "%{$query}%")
        ->orWhere('last_name', 'LIKE', "%{$query}%")
        ->limit(10)
        ->get(['id', 'first_name', 'middle_name', 'last_name']);

    return response()->json($clients);
});

