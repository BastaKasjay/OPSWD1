<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $reports = collect(); // Default empty collection

        if ($from && $to) {
            // Served clients (Approved claims + disbursement in range)
            $reports = DB::table('clients')
                ->join('claims', 'clients.id', '=', 'claims.client_id')
                ->join('disbursements', 'claims.id', '=', 'disbursements.claim_id')
                ->join('client_assistance', 'clients.id', '=', 'client_assistance.client_id')
                ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
                ->select(
                    'clients.municipality',
                    DB::raw('COUNT(DISTINCT clients.id) as served_clients'),
                    DB::raw("SUM(CASE WHEN clients.sex = 'Male' THEN 1 ELSE 0 END) as male"),
                    DB::raw("SUM(CASE WHEN clients.sex = 'Female' THEN 1 ELSE 0 END) as female"),
                    DB::raw("SUM(CASE WHEN client_assistance.case_type = 'CKD' THEN 1 ELSE 0 END) as CKD"),
                    DB::raw("SUM(CASE WHEN client_assistance.case_type = 'Cancer' THEN 1 ELSE 0 END) as Cancer"),
                    DB::raw("SUM(CASE WHEN client_assistance.case_type = 'Stroke' THEN 1 ELSE 0 END) as Stroke"),
                    DB::raw("SUM(CASE WHEN client_assistance.case_type = 'Other' THEN 1 ELSE 0 END) as Other"),
                    DB::raw("SUM(CASE WHEN assistance_types.type_name = 'LGU' THEN disbursements.amount ELSE 0 END) as LGUAmount"),
                    DB::raw("SUM(CASE WHEN assistance_types.type_name = 'PCSO' THEN disbursements.amount ELSE 0 END) as PCSOAmount"),
                    DB::raw("SUM(CASE WHEN assistance_types.type_name = 'DOH' THEN disbursements.amount ELSE 0 END) as DOHAmount"),
                    DB::raw("SUM(disbursements.amount) as TotalAmountPaid")
                )
                ->where('claims.status', 'approved')
                ->whereBetween('disbursements.date_received_claimed', [$from, $to])
                ->groupBy('clients.municipality')
                ->get();

            // Unserved clients (Disapproved due to claiming already this year)
            $unserved = DB::table('clients')
                ->join('claims', 'clients.id', '=', 'claims.client_id')
                ->select(
                    'clients.municipality',
                    DB::raw('COUNT(DISTINCT clients.id) as unserved_clients')
                )
                ->where('claims.status', 'disapproved')
                ->whereBetween('claims.updated_at', [$from, $to])
                ->where('claims.remarks', 'like', '%already claimed this year%')
                ->groupBy('clients.municipality')
                ->get()
                ->keyBy('municipality');

            // Merge unserved into main reports
            $reports = $reports->map(function ($report) use ($unserved) {
                $mun = $report->municipality;
                $report->unserved_clients = $unserved[$mun]->unserved_clients ?? 0;
                return $report;
            });
        }

        return view('reports.index', compact('reports', 'from', 'to'));
    }


}
