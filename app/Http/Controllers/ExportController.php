<?php

namespace App\Http\Controllers;

use App\Models\Disbursement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class ExportController extends Controller
{
    public function downloadExcel()
    {
        $filename = "grouped_payouts.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            "Client Name", "Representative", "Contact", "Municipality", "Amount Approved",
            "Date Received", "Date Released", "Claim Status", "Payment Method", "Payout Date"
        ];

        $callback = function() use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            // Use the same filtering logic as web view and PDF
            $claims = \App\Models\Claim::with(['client', 'client.payee', 'client.municipality', 'disbursement'])
                ->where('status', 'approved')
                ->whereNotNull('payout_date')
                ->whereNotNull('form_of_payment')
                ->get();

            foreach ($claims as $claim) {
                fputcsv($handle, [
                    $claim->client->full_name ?? '-',
                    $claim->client->payee->full_name ?? '-',
                    $claim->client->payee && !$claim->client->payee->is_self_payee
                        ? $claim->client->payee->contact_number
                        : $claim->client->contact_number,
                    $claim->client->municipality->name ?? '-',
                    '₱' . number_format($claim->amount_approved ?? 0, 2),
                    $claim->disbursement->date_received_claimed ?? '-',
                    ucfirst($claim->disbursement->claim_status ?? 'pending'),
                    ucfirst($claim->form_of_payment ?? '-'),
                    $claim->payout_date
                        ? \Carbon\Carbon::parse($claim->payout_date)->format('F d, Y')
                        : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    
    public function downloadPDF(Request $request)
{
    $method = $request->input('method');
    $date = $request->input('date');
    $status = $request->input('status');

    // Start with the same base query as the web view
    $query = \App\Models\Claim::with(['client', 'client.payee', 'client.municipality', 'disbursement'])
        ->where('status', 'approved')
        ->whereNotNull('payout_date')
        ->whereNotNull('form_of_payment');

    if ($method) {
        $query->where('form_of_payment', $method);
    }

    if ($date) {
        $query->whereDate('payout_date', $date);
    }

    if ($status) {
        $query->whereHas('disbursement', function ($q) use ($status) {
            $q->where('claim_status', $status);
        });
    }

    $filteredClaims = $query->get();

    $columns = [
        "Client Name", "Representative", "Contact", "Municipality", "Amount Approved",
        "Date Received", "Date Released", "Claim Status", "Payment Method", "Payout Date"
    ];

    $pdf = Pdf::loadView('claims.groupedReport.groupedReports', [
        'columns' => $columns,
        'allClaims' => $filteredClaims,  // Use filtered results here!
    ])->setPaper('a4', 'landscape');

    return $pdf->download('grouped_payouts.pdf');
}


    // Report Excel
    public function downloadReport(Request $request)
    {
        $quarter = $request->input('quarter');
        $year = $request->input('year', now()->year);

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

        // Same data query logic as index()
        $reportData = DB::table('clients')
            ->join('claims', 'clients.id', '=', 'claims.client_id')
            ->join('disbursements', 'claims.id', '=', 'disbursements.claim_id')
            ->join('municipalities', 'clients.municipality_id', '=', 'municipalities.id')
            ->join('client_assistance', 'claims.client_assistance_id', '=', 'client_assistance.id')
            ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
            ->select(
                'municipalities.name as municipality',
                DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'male' THEN claims.id ELSE NULL END) as male"),
                DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'female' THEN claims.id ELSE NULL END) as female"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'CKD' THEN claims.id END) as CKD"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'Cancer' THEN claims.id END) as Cancer"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'Heart Illness' THEN claims.id END) as HeartIllness"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case LIKE '%Diabetes%' OR client_assistance.medical_case LIKE '%Hypertension%' THEN claims.id END) as DiabetesHypertension"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case NOT IN ('CKD','Cancer','Heart Illness') AND client_assistance.medical_case NOT LIKE '%Diabetes%' AND client_assistance.medical_case NOT LIKE '%Hypertension%' THEN claims.id END) as OtherMedical"),
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
                DB::raw('COUNT(claims.id) as unserved_clients')
            )
            ->get()
            ->keyBy('municipality');

        $allMunicipalities = collect($reportData->pluck('municipality'))
            ->merge($unserved->keys())
            ->unique();

        $finalData = $allMunicipalities->map(function ($municipality) use ($reportData, $unserved) {
            $row = $reportData->firstWhere('municipality', $municipality);

            return [
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

        // CSV Headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report_' . $quarter . '_' . $year . '.csv"',
        ];

        // CSV download response
        $callback = function () use ($finalData) {
            $file = fopen('php://output', 'w');

            // Write CSV header row
            fputcsv($file, [
                'No', 'Municipality', 'Male', 'Female',
                'CKD', 'Cancer', 'Heart Illness', 'Diabetes & Hypertension', 'Others',
                'Reg-Med', 'Reg-Burial', 'Reg-ESA',
                'Sen-Med', 'Sen-Burial',
                'PDRRM-ESA', 'Amount', 'Unserved'
            ]);

            foreach ($finalData as $index => $data) {
                fputcsv($file, [
                    $index + 1,
                    $data['municipality'],
                    $data['male'],
                    $data['female'],
                    $data['CKD'],
                    $data['Cancer'],
                    $data['HeartIllness'],
                    $data['DiabetesHypertension'],
                    $data['OtherMedical'],
                    $data['RegularMedical'],
                    $data['RegularBurial'],
                    $data['RegularESA'],
                    $data['SeniorMedical'],
                    $data['SeniorBurial'],
                    $data['PDRRMESA'],
                    $data['TotalAmountPaid'],
                    $data['unserved_clients'],
                ]);
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

///////////////////////////////////////////////////////////

// Reports PDF
    public function downloadReportPdf(Request $request)
    {
        $quarter = $request->input('quarter');
        $year = $request->input('year', now()->year);

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

                // Copy data logic from downloadReport()
        $reportData = DB::table('clients')
            ->join('claims', 'clients.id', '=', 'claims.client_id')
            ->join('disbursements', 'claims.id', '=', 'disbursements.claim_id')
            ->join('municipalities', 'clients.municipality_id', '=', 'municipalities.id')
            ->join('client_assistance', 'claims.client_assistance_id', '=', 'client_assistance.id')
            ->join('assistance_types', 'client_assistance.assistance_type_id', '=', 'assistance_types.id')
            ->select(
                'municipalities.name as municipality',
                DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'male' THEN claims.id ELSE NULL END) as male"),
                DB::raw("COUNT(DISTINCT CASE WHEN clients.sex = 'female' THEN claims.id ELSE NULL END) as female"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'CKD' THEN claims.id END) as CKD"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'Cancer' THEN claims.id END) as Cancer"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case = 'Heart Illness' THEN claims.id END) as HeartIllness"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case LIKE '%Diabetes%' OR client_assistance.medical_case LIKE '%Hypertension%' THEN claims.id END) as DiabetesHypertension"),
                DB::raw("COUNT(DISTINCT CASE WHEN client_assistance.medical_case NOT IN ('CKD','Cancer','Heart Illness') AND client_assistance.medical_case NOT LIKE '%Diabetes%' AND client_assistance.medical_case NOT LIKE '%Hypertension%' THEN claims.id END) as OtherMedical"),
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
                DB::raw('COUNT(claims.id) as unserved_clients')
            )
            ->get()
            ->keyBy('municipality');

        $allMunicipalities = collect($reportData->pluck('municipality'))
            ->merge($unserved->keys())
            ->unique();

        $finalData = $allMunicipalities->map(function ($municipality) use ($reportData, $unserved) {
            $row = $reportData->firstWhere('municipality', $municipality);

            return [
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

        // Calculate totals (same as ReportController)
        $totals = [
            'male' => collect($finalData)->sum('male'),
            'female' => collect($finalData)->sum('female'),
            'CKD' => collect($finalData)->sum('CKD'),
            'Cancer' => collect($finalData)->sum('Cancer'),
            'HeartIllness' => collect($finalData)->sum('HeartIllness'),
            'DiabetesHypertension' => collect($finalData)->sum('DiabetesHypertension'),
            'OtherMedical' => collect($finalData)->sum('OtherMedical'),
            'RegularMedical' => collect($finalData)->sum('RegularMedical'),
            'RegularBurial' => collect($finalData)->sum('RegularBurial'),
            'RegularESA' => collect($finalData)->sum('RegularESA'),
            'SeniorMedical' => collect($finalData)->sum('SeniorMedical'),
            'SeniorBurial' => collect($finalData)->sum('SeniorBurial'),
            'PDRRMESA' => collect($finalData)->sum('PDRRMESA'),
            'TotalAmountPaid' => collect($finalData)->sum('TotalAmountPaid'),
            'unserved_clients' => collect($finalData)->sum('unserved_clients'),
        ];

        $pdf = Pdf::loadView('reports.QuarterlyReport.aicsQuarterlyReports', [
            'reportData' => $finalData,
            'totals' => $totals,
            'quarter' => $quarter,
            'year' => $year,
            'pdf' => true // Optional: flag for view logic
        ])->setPaper('a4', 'landscape');

        return $pdf->download("AICS_Report_{$year}_{$quarter}.pdf");
    }

}
