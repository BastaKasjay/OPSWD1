<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\Client;
use App\Models\ClientAssistance;

class ClaimController extends Controller
{
    public function updateStatus(Request $request, $id)
{
    $status = $request->input('status');

    try {
        $claim = Claim::findOrFail($id);

        // Change status
        $claim->status = (string) $status;

        // Clear or set reason
        if ($status === 'disapproved') {
            $claim->reason_of_disapprovement = $request->input('reason_of_disapprovement');
        } else {
            $claim->reason_of_disapprovement = null; // clear if approved or pending
        }

        $claim->save();

        return redirect()->back()->with('success', 'Status updated to ' . ucfirst($status) . '.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}




    public function update(Request $request, $id)
{
    // Validation (nullable so you can update fields one by one)
    $request->validate([
        'form_of_payment' => 'nullable|in:cash,cheque',
        'check_no' => 'nullable|required_if:form_of_payment,cheque',
        'payout_date' => 'nullable|date',
        'source_of_fund' => 'nullable|in:Regular,Senior,PDRRM',
    ]);

    // Fetch claim with related client and disbursement
    $claim = Claim::with(['client', 'disbursement', 'clientAssistance.assistanceType'])
              ->findOrFail($id);


    // Only update fields that were actually filled
    $claim->date_cafoa_prepared = $request->filled('date_cafoa_prepared') ? $request->input('date_cafoa_prepared') : $claim->date_cafoa_prepared;
    $claim->date_pgo_received   = $request->filled('date_pgo_received')   ? $request->input('date_pgo_received')   : $claim->date_pgo_received;
    $claim->amount_approved     = $request->filled('amount_approved')     ? $request->input('amount_approved')     : $claim->amount_approved;
    $claim->form_of_payment     = $request->filled('form_of_payment')     ? $request->input('form_of_payment')     : $claim->form_of_payment;
    $claim->payout_date         = $request->filled('payout_date')         ? $request->input('payout_date')         : $claim->payout_date;
    $claim->source_of_fund = $request->input('source_of_fund', $claim->source_of_fund);

    // Clear reason if not disapproved
    if ($claim->status === 'disapproved') {
        $claim->reason_of_disapprovement = $request->input('reason_of_disapprovement', $claim->reason_of_disapprovement);
    } else {
        $claim->reason_of_disapprovement = null; // Clear reason if approved or pending
    }

    $claim->save();

    // Prepare variables for payment IDs
    $cashPaymentId = null;
    $checkPaymentId = null;

    $requiresAmount = optional($claim->clientAssistance->assistanceType)->type_name !== 'Transportation Assistance';

        // Allow disbursement even without amount if type is Transportation
        if ($claim->form_of_payment && $claim->payout_date && (!$requiresAmount || $claim->amount_approved)) {

        // Cash Payment
        if ($claim->form_of_payment === 'cash') {
            $cashPayment = \App\Models\CashPayment::updateOrCreate(
                ['claim_id' => $claim->id],
                [
                    'client_id' => $claim->client_id,
                    'date_prepared' => now(),
                    'confirmed_people' => [$claim->client?->payee?->full_name ?? $claim->client?->full_name],
                    'amount_confirmed' => $claim->amount_approved,
                    'total_amount_withdrawn' => $claim->amount_approved,
                    'date_of_payout' => $claim->payout_date,
                ]
            );
            $cashPaymentId = $cashPayment->id;
            \App\Models\CheckPayment::where('claim_id', $claim->id)->delete();
        }

        // Check Payment
        elseif ($claim->form_of_payment === 'cheque') {
            $checkPayment = \App\Models\CheckPayment::updateOrCreate(
                ['claim_id' => $claim->id],
                [
                    'client_id' => $claim->client_id,
                    'date_prepared' => now(),
                    'amount' => $claim->amount_approved,
                    'check_no' => $request->input('check_no'),
                    'date_of_payout' => $claim->payout_date,
                ]
            );
            $checkPaymentId = $checkPayment->id;
            \App\Models\CashPayment::where('claim_id', $claim->id)->delete();
        }

        // Disbursement Record
        \App\Models\Disbursement::updateOrCreate(
            ['claim_id' => $claim->id],
            [
                'client_id' => $claim->client_id,
                'cash_payment_id' => $cashPaymentId,
                'check_payment_id' => $checkPaymentId,
                'form_of_payment' => $claim->form_of_payment,
                'payout_date' => $claim->payout_date,
                'amount' => $requiresAmount ? $claim->amount_approved : 0, 
                'claim_status' => optional($claim->disbursement)->claim_status ?? 'pending',
                'date_received_claimed' => optional($claim->disbursement)->date_received_claimed,
                'date_released' => optional($claim->disbursement)->date_released,
                'total_amount_claimed' => optional($claim->disbursement)->total_amount_claimed,
            ]
        );
    }

    return redirect()->back()->with('success', 'Claim information updated successfully.');
}







    public function groupedClaims()
    {
        // Load claims with related client, payee, assistance, and disbursement
        $claims = Claim::with(['client.payee', 'client.municipality', 'clientAssistance', 'disbursement'])
            ->where('status', 'approved')
            ->whereNotNull('payout_date')
            ->whereNotNull('form_of_payment')
            ->get();

        // Group by payout date and form of payment
        $grouped = $claims->groupBy([
            fn($item) => $item->payout_date,
            fn($item) => $item->form_of_payment
        ]);

        return view('claims.grouped', compact('grouped'));
    }


}
