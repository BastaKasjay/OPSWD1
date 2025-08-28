<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ExportController;
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


// ✅ Protected routes
Route::middleware('auth')->group(function () {

    Route::get('/home', [DashboardController::class, 'index'])->name('home');
Route::post('/budgets', [DashboardController::class, 'storeBudget'])->name('budgets.store');

Route::get('/claims/grouped', [ClaimController::class, 'groupedClaims'])->name('claims.grouped');
Route::patch('/claims/{id}/update-status', [ClaimController::class, 'updateStatus'])->name('claims.update-status');
Route::put('/claims/{id}', [ClaimController::class, 'update'])->name('claims.update');

Route::get('/clients/assistance', [ClientAssistanceController::class, 'assistance'])->name('clients.assistance');
Route::resource('clients', ClientController::class);
Route::put('/clients/{id}/assistance', [ClientController::class, 'updateAssistance'])
    ->name('clients.updateAssistance');

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

// Excel & PDF download routes for Grouped.blade
Route::get('/grouped-payouts/download/excel', [ExportController::class, 'downloadExcel'])->name('grouped-payouts.download.excel');
Route::get('/grouped-payouts/download/pdf', [ExportController::class, 'downloadPdf'])->name('grouped-payouts.download.pdf');

// Excel & PDF download routes for Reports
Route::get('/reports/download/excel', [ExportController::class, 'downloadReport'])->name('reports.download.excel');
Route::get('/reports/download/pdf', [ExportController::class, 'downloadReportPdf'])->name('reports.download.pdf');

}); // End of auth middleware group