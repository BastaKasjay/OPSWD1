<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        ->join('client_assistance', 'clients.id', '=', 'client_assistance.client_id')
        ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
        ->join('assistance_categories', 'assistance_types.id', '=', 'assistance_categories.assistance_type_id')
        ->select(
            'municipalities.name as municipality',
            DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'male' THEN clients.id ELSE NULL END) as male"),
            DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'female' THEN clients.id ELSE NULL END) as female"),
            DB::raw("SUM(CASE WHEN client_assistance.medical_case = 'CKD' THEN 1 ELSE 0 END) as CKD"),
            DB::raw("SUM(CASE WHEN client_assistance.medical_case = 'Cancer' THEN 1 ELSE 0 END) as Cancer"),
            DB::raw("SUM(CASE WHEN client_assistance.medical_case = 'Heart Illness' THEN 1 ELSE 0 END) as HeartIllness"),
            DB::raw("SUM(CASE WHEN client_assistance.medical_case = 'Diabetes & Hypertension' THEN 1 ELSE 0 END) as DiabetesHypertension"),
            DB::raw("SUM(CASE WHEN client_assistance.medical_case = 'Other' THEN 1 ELSE 0 END) as OtherMedical"),

            // Replace 'clients.category' with 'assistance_categories.category_name'
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'Medical' AND assistance_categories.category_name = 'Regular' THEN 1 ELSE 0 END) as RegularMedical"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'Burial' AND assistance_categories.category_name = 'Regular' THEN 1 ELSE 0 END) as RegularBurial"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'ESA' AND assistance_categories.category_name = 'Regular' THEN 1 ELSE 0 END) as RegularESA"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'Medical' AND assistance_categories.category_name = 'Senior Citizen' THEN 1 ELSE 0 END) as SeniorMedical"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'Burial' AND assistance_categories.category_name = 'Senior Citizen' THEN 1 ELSE 0 END) as SeniorBurial"),
            DB::raw("SUM(CASE WHEN assistance_types.type_name = 'ESA' AND assistance_categories.category_name = 'PDRRM' THEN 1 ELSE 0 END) as PDRRMESA"),

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
        ->where(function ($query) use ($from, $to) {
            $query->whereBetween('claims.updated_at', [$from, $to])
                ->orWhere(function ($q) use ($from, $to) {
                    $q->whereNull('claims.updated_at')
                        ->whereBetween('claims.created_at', [$from, $to]);
                });
        })
        ->groupBy('municipalities.name')
        ->select(
            'municipalities.name as municipality',
            DB::raw('COUNT(DISTINCT claims.client_id) as unserved_clients')
        )
        ->get()
        ->keyBy('municipality');




        // Merge unserved clients to reportData
        $reportData->transform(function ($item) use ($unserved) {
            $item->unserved_clients = $unserved[$item->municipality]->unserved_clients ?? 0;
            return $item;
        });

        // Totals calculation
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

        // dd($from, $to);

        // dd($totals);

        // dd($reportData->first());


        return view('reports.index', compact('reportData', 'totals'));
    }
}
