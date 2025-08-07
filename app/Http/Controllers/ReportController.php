<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $quarter = $request->input('quarter');
        $year = $request->input('year', now()->year);

        // Define quarter ranges
        switch ($quarter) {
            case 'Q1':
                $from = Carbon::create($year, 1, 1)->startOfDay();
                $to = Carbon::create($year, 3, 31)->endOfDay();
                break;
            case 'Q2':
                $from = Carbon::create($year, 4, 1)->startOfDay();
                $to = Carbon::create($year, 6, 30)->endOfDay();
                break;
            case 'Q3':
                $from = Carbon::create($year, 7, 1)->startOfDay();
                $to = Carbon::create($year, 9, 30)->endOfDay();
                break;
            case 'Q4':
                $from = Carbon::create($year, 10, 1)->startOfDay();
                $to = Carbon::create($year, 12, 31)->endOfDay();
                break;
            default:
                $from = Carbon::create($year, 1, 1)->startOfDay();
                $to = Carbon::create($year, 12, 31)->endOfDay();
                break;
        }



        

        $reportData = DB::table('clients')
        ->join('claims', 'clients.id', '=', 'claims.client_id')
        ->join('disbursements', 'claims.id', '=', 'disbursements.claim_id')
        ->join('municipalities', 'clients.municipality_id', '=', 'municipalities.id')
        ->join('client_assistance', 'claims.client_assistance_id', '=', 'client_assistance.id')
        ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')

        ->select(
            'municipalities.name as municipality',
            DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'male' THEN clients.id ELSE NULL END) as male"),
            DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'female' THEN clients.id ELSE NULL END) as female"),
            DB::raw("COUNT(DISTINCT CASE 
                WHEN client_assistance.medical_case = 'CKD' 
                THEN clients.id END) as CKD"),

            DB::raw("COUNT(DISTINCT CASE 
                WHEN client_assistance.medical_case = 'Cancer' 
                THEN clients.id END) as Cancer"),

            DB::raw("COUNT(DISTINCT CASE 
                WHEN client_assistance.medical_case = 'Heart Illness' 
                THEN clients.id END) as HeartIllness"),

            DB::raw("COUNT(DISTINCT CASE 
                WHEN client_assistance.medical_case LIKE '%Diabetes%' 
                    OR client_assistance.medical_case LIKE '%Hypertension%' 
                THEN clients.id END) as DiabetesHypertension"),

            DB::raw("COUNT(DISTINCT CASE 
                WHEN client_assistance.medical_case NOT IN ('CKD','Cancer','Heart Illness')
                    AND client_assistance.medical_case NOT LIKE '%Diabetes%' 
                    AND client_assistance.medical_case NOT LIKE '%Hypertension%' 
                THEN clients.id END) as OtherMedical"),




            // Count by claim's source_of_fund instead of category_name
            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%Medical%' AND claims.source_of_fund = 'Regular' THEN 1 ELSE 0 END) as RegularMedical"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%Burial%' AND claims.source_of_fund = 'Regular' THEN 1 ELSE 0 END) as RegularBurial"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%ESA%' AND claims.source_of_fund = 'Regular' THEN 1 ELSE 0 END) as RegularESA"),

            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%Medical%' AND claims.source_of_fund = 'Senior' THEN 1 ELSE 0 END) as SeniorMedical"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%Burial%' AND claims.source_of_fund = 'Senior' THEN 1 ELSE 0 END) as SeniorBurial"),

            DB::raw("SUM(CASE WHEN assistance_types.type_name LIKE '%ESA%' AND claims.source_of_fund = 'PDRRM' THEN 1 ELSE 0 END) as PDRRMESA"),


            DB::raw("SUM(disbursements.amount) as TotalAmountPaid")


        )
        ->where('disbursements.claim_status', '=', 'claimed')
        ->whereNotNull('disbursements.date_received_claimed')
        ->whereNotNull('disbursements.date_released')
        ->whereBetween('disbursements.date_received_claimed', [$from, $to])
        ->groupBy('municipalities.name')
        ->get();


        $unserved = DB::table('claims')
        ->join('clients', 'claims.client_id', '=', 'clients.id')
        ->join('municipalities', 'clients.municipality_id', '=', 'municipalities.id')
        ->where('claims.status', 'disapproved')
        ->whereBetween('claims.updated_at', [$from, $to])
        ->groupBy('municipalities.name')
        ->select(
            'municipalities.name as municipality',
            DB::raw('COUNT(DISTINCT claims.client_id) as unserved_clients')
        )
        ->get()
        ->keyBy('municipality');


        //Combine all municipalities (those with claimed OR unserved)
        $allMunicipalities = collect($reportData->pluck('municipality'))
            ->merge($unserved->keys())
            ->unique();

        //Build a new dataset including municipalities that only have unserved clients
        $reportData = $allMunicipalities->map(function ($municipality) use ($reportData, $unserved) {
            $row = $reportData->firstWhere('municipality', $municipality);

            return (object) [
                'municipality' => $municipality,
                'male' => $row->male ?? 0,
                'female' => $row->female ?? 0,
                'CKD' => $row->CKD ?? 0,
                'Cancer' => $row->Cancer ?? 0,
                'HeartIllness' => $row->HeartIllness ?? 0,
                'DiabetesHypertension' => $row->DiabetesHypertension ?? 0,
                'OtherMedical' => $row->OtherMedical ?? 0,
                'RegularMedical' => $row->RegularMedical ?? 0,
                'RegularBurial' => $row->RegularBurial ?? 0,
                'RegularESA' => $row->RegularESA ?? 0,
                'SeniorMedical' => $row->SeniorMedical ?? 0,
                'SeniorBurial' => $row->SeniorBurial ?? 0,
                'PDRRMESA' => $row->PDRRMESA ?? 0,
                'TotalAmountPaid' => $row->TotalAmountPaid ?? 0,
                'unserved_clients' => $unserved[$municipality]->unserved_clients ?? 0,
            ];
        });
        

        //Now compute totals AFTER merging
        $totals = [
            'male' => $reportData->sum('male'),
            'female' => $reportData->sum('female'),
            'ckd' => $reportData->sum('CKD'),
            'cancer' => $reportData->sum('Cancer'),
            'heart_illness' => $reportData->sum('HeartIllness'),
            'diabetes_hypertension' => $reportData->sum('DiabetesHypertension'),
            'others' => $reportData->sum('OtherMedical'),
            'regular_medical' => $reportData->sum('RegularMedical'),
            'regular_burial' => $reportData->sum('RegularBurial'),
            'regular_esa' => $reportData->sum('RegularESA'),
            'senior_medical' => $reportData->sum('SeniorMedical'),
            'senior_burial' => $reportData->sum('SeniorBurial'),
            'pdrrm_esa' => $reportData->sum('PDRRMESA'),
            'amount_total' => $reportData->sum('TotalAmountPaid'),
            'unreserved' => $reportData->sum('unserved_clients'),
        ];


  


        // Manual Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        $paginated = new LengthAwarePaginator(
            $reportData->forPage($page, $perPage),
            $reportData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('reports.index', [
            'reportData' => $paginated,
            'totals' => $totals
        ]);
    }
}
