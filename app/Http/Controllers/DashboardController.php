<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;    
use App\Models\Client;
use App\Models\Claim;
use App\Models\Disbursement;
use App\Models\ClientAssistance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(Request $request)
{
    $year = $request->input('year');
    $month = $request->input('month');

    // Helper to filter by year/month on a column
    $filterByDate = function ($query, $column = 'payout_date') use ($year, $month) {
        if ($year) $query->whereYear($column, $year);
        if ($month) $query->whereMonth($column, $month);
    };

    $totalClients = Client::when($year, fn($q) => $q->whereYear('created_at', $year))
                      ->when($month, fn($q) => $q->whereMonth('created_at', $month))
                      ->count();


    $totalPayouts = Disbursement::whereNotNull('date_received_claimed')
        ->when($year || $month, fn($q) => $filterByDate($q, 'payout_date'))
        ->count();

    $disbursedAmount = Disbursement::whereNotNull('date_received_claimed')
        ->when($year || $month, fn($q) => $filterByDate($q, 'payout_date'))
        ->sum('amount');

    $servedClients = Claim::where('status', 'approved')
        ->when($year || $month, fn($q) => $q->whereHas('disbursement', $filterByDate))
        ->count();

    $unservedClients = Claim::where('status', 'disapproved')
    ->when($year, fn($q) => $q->whereYear('created_at', $year))
    ->when($month, fn($q) => $q->whereMonth('created_at', $month))
    ->distinct('client_id')
    ->count('client_id');


    $unservedPercentage = $totalClients > 0
        ? round(($unservedClients / $totalClients) * 100, 2)
        : 0;

    $servedByType = ClientAssistance::with('assistanceType')
        ->whereHas('claim', fn($q) => $q->where('status', 'approved')
            ->when($year || $month, fn($q2) => $q2->whereHas('disbursement', $filterByDate)))
        ->selectRaw('assistance_type_id, COUNT(*) as total')
        ->groupBy('assistance_type_id')
        ->get();



    $previousPayouts = Disbursement::select('payout_date', DB::raw('COUNT(*) as total_claims'))
        ->where('claim_status', 'claimed')
        ->whereNotNull('payout_date')
        ->when($year || $month, fn($q) => $filterByDate($q, 'payout_date'))
        ->groupBy('payout_date')
        ->orderBy('payout_date', 'desc')
        ->take(10)
        ->get();

    $scheduledPayouts = Disbursement::with(['client.municipality', 'client.payee'])
        ->whereNotNull('payout_date')
        ->whereDate('payout_date', '>=', now())
        ->when($year || $month, fn($q) => $filterByDate($q, 'payout_date'))
        ->orderBy('payout_date', 'asc')
        ->get();

    // Vulnerability, Age Groups, and Cases
    $vulnerabilityData = Disbursement::where('claim_status', 'claimed')
        ->when($year || $month, fn($q) => $filterByDate($q, 'payout_date'))
        ->join('clients', 'clients.id', '=', 'disbursements.client_id')
        ->join('client_vulnerability_sector', 'clients.id', '=', 'client_vulnerability_sector.client_id')
        ->join('vulnerability_sectors', 'vulnerability_sectors.id', '=', 'client_vulnerability_sector.vulnerability_sector_id')
        ->select('vulnerability_sectors.name', DB::raw('COUNT(*) as total'))
        ->groupBy('vulnerability_sectors.name')
        ->pluck('total', 'vulnerability_sectors.name');

    $ageGroupData = DB::table('clients')
        ->join('disbursements', 'clients.id', '=', 'disbursements.client_id')
        ->where('disbursements.claim_status', 'claimed')
        ->when($year || $month, fn($q) => $filterByDate($q, 'disbursements.payout_date'))
        ->selectRaw("
            CASE
                WHEN clients.age BETWEEN 0 AND 18 THEN '0-18'
                WHEN clients.age BETWEEN 19 AND 35 THEN '19-35'
                WHEN clients.age BETWEEN 36 AND 60 THEN '36-59'
                ELSE '60+'
            END as age_group,
            COUNT(*) as total
        ")
        ->groupBy('age_group')
        ->pluck('total', 'age_group');

    $caseData = DB::table('clients')
        ->join('disbursements', 'clients.id', '=', 'disbursements.client_id')
        ->join('client_assistance', 'clients.id', '=', 'client_assistance.client_id')
        ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
        ->where('disbursements.claim_status', 'claimed')
        ->when($year || $month, fn($q) => $filterByDate($q, 'disbursements.payout_date'))
        ->where('assistance_types.type_name', 'Medical Assistance')
        ->whereNotNull('client_assistance.medical_case')
        ->select('client_assistance.medical_case as case', DB::raw('COUNT(*) as total'))
        ->groupBy('client_assistance.medical_case')
        ->pluck('total', 'case');

    $budgets = Budget::when($year, fn($q) => $q->where('year', $year))
                 ->get();
    
    $budgetPerType = $budgets->groupBy('type')
        ->map(fn($items) => $items->sum('allocated_amount'));

    $totalBudget = $budgets->sum('allocated_amount');

    $disbursedPerType = Claim::whereHas('disbursement', fn($q) =>
            $q->whereNotNull('date_received_claimed')
              ->when($year || $month, fn($q2) => $filterByDate($q2)))
        ->selectRaw('source_of_fund, SUM(amount_approved) as total_disbursed')
        ->groupBy('source_of_fund')
        ->pluck('total_disbursed', 'source_of_fund');

    return view('HomePage.home', compact(
        'totalClients',
        'totalPayouts',
        'disbursedAmount',
        'servedClients',
        'unservedPercentage',
        'servedByType',
        'vulnerabilityData',
        'ageGroupData',
        'caseData',
        'budgets',
        'budgetPerType',   // add this
        'totalBudget',
        'disbursedPerType',
        'previousPayouts',
        'scheduledPayouts'
    ));
}


    public function storeBudget(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'type' => 'required|in:Regular,Senior,PDRRM,Supplemental',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['year' => $request->year, 'type' => $request->type],
            ['allocated_amount' => $request->allocated_amount]
        );

        return redirect()->back()->with('success', 'Budget saved successfully!');
    }



}
