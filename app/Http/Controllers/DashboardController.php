<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;    
use App\Models\Client;
use App\Models\Claim;
use App\Models\Disbursement;
use App\Models\ClientAssistance;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();

        
        $totalPayouts = Disbursement::whereNotNull('date_received_claimed')->count();

        
        $disbursedAmount = Disbursement::whereNotNull('date_received_claimed')->sum('amount');

    
        $servedClients = Claim::where('status', 'approved')->count();

        
        $unservedClients = Claim::where('status', 'disapproved')
        ->distinct('client_id')
        ->count('client_id');

        $unservedPercentage = $totalClients > 0 
        ? round(($unservedClients / $totalClients) * 100, 2) 
        : 0;

    
        $servedByType = ClientAssistance::with('assistanceType')
            ->whereHas('claim', fn($q) => $q->where('status', 'approved'))
            ->selectRaw('assistance_type_id, COUNT(*) as total')
            ->groupBy('assistance_type_id')
            ->get();

        $upcomingPayouts = Disbursement::with(['client.municipality', 'client.payee'])
        ->whereNotNull('payout_date') 
        ->whereDate('payout_date', '>=', now())
        ->where(function($q) {
            $q->whereNull('date_received_claimed') 
            ->orWhere('claim_status', '!=', 'claimed');
        })
        ->orderBy('payout_date', 'asc')
        ->take(5)
        ->get();



    // dd(Disbursement::select('id', 'payout_date', 'claim_status', 'date_received_claimed')->get());
    // dd($upcomingPayouts->toArray());

        //  Served Clients Grouped by Vulnerability
            $vulnerabilityData = Disbursement::where('claim_status', 'claimed')
                ->join('clients', 'clients.id', '=', 'disbursements.client_id')
                ->join('client_vulnerability_sector', 'clients.id', '=', 'client_vulnerability_sector.client_id')
                ->join('vulnerability_sectors', 'vulnerability_sectors.id', '=', 'client_vulnerability_sector.vulnerability_sector_id')
                ->select('vulnerability_sectors.name', DB::raw('COUNT(*) as total'))
                ->groupBy('vulnerability_sectors.name')
                ->pluck('total', 'vulnerability_sectors.name');

            //  Get served clients by age group
            $ageGroupData = DB::table('clients')
                ->join('disbursements', 'clients.id', '=', 'disbursements.client_id')
                ->where('disbursements.claim_status', 'claimed')
                ->selectRaw("
                    CASE
                        WHEN clients.age BETWEEN 0 AND 18 THEN '0-18'
                        WHEN clients.age BETWEEN 19 AND 35 THEN '19-35'
                        WHEN clients.age BETWEEN 36 AND 60 THEN '36-60'
                        ELSE '60+'
                    END as age_group,
                    COUNT(*) as total
                ")
                ->where('disbursements.claim_status', 'claimed')
                ->groupBy('age_group')
                ->pluck('total', 'age_group');

            $caseData = DB::table('clients')
    ->join('disbursements', 'clients.id', '=', 'disbursements.client_id')
    ->join('client_assistance', 'clients.id', '=', 'client_assistance.client_id')
    ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
    ->where('disbursements.claim_status', 'claimed')
    ->where('assistance_types.type_name', 'Medical Assistance')
    ->whereNotNull('client_assistance.medical_case') // ✅ ensure only rows with a medical case
    ->select('client_assistance.medical_case as case', DB::raw('COUNT(*) as total'))
    ->groupBy('client_assistance.medical_case')
    ->pluck('total', 'case');



        $budgets = Budget::where('year', date('Y'))->get();

        $disbursedPerType = Claim::whereHas('disbursement', fn($q) => 
                $q->whereNotNull('date_received_claimed'))
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
            'upcomingPayouts',
            'vulnerabilityData',
            'ageGroupData',
            'caseData',
            'budgets',
            'disbursedPerType'
        ));

        
    }

    public function storeBudget(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'type' => 'required|in:Regular,Senior,PDRRM',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['year' => $request->year, 'type' => $request->type],
            ['allocated_amount' => $request->allocated_amount]
        );

        return redirect()->back()->with('success', 'Budget saved successfully!');
    }



}
