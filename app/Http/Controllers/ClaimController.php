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

            // Only update if status has changed
            if ($claim->status !== $status) {
                $claim->status = (string) $status;
                $claim->date_status_updated = now(); // <-- log the date of status change
            }

            // Clear or set reason
            if ($status === 'disapproved') {
                $claim->reason_of_disapprovement = $request->input('reason_of_disapprovement');
            } else {
                $claim->reason_of_disapprovement = null;
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

    // dd($request->all());
    // $request->validate([
    //     'form_of_payment' => 'nullable|in:cash,cheque',
    //     'check_no' => 'nullable|required_if:form_of_payment,cheque',a
    //     'payout_date' => 'nullable|date',
    //     'source_of_fund' => 'nullable|in:Regular,Senior,PDRRM',
    // ]);

    // Fetch claim with related client and disbursement
    $claim = Claim::with(['client', 'disbursement', 'clientAssistance.assistanceType'])
              ->findOrFail($id);

     // Prevent update if not approved
    if (!in_array($claim->status, ['approved', 'disapproved'])) {
    return redirect()->back()->with(
        'error', 
        'Claim information can only be updated when the status is approved or disapproved.'
    );
}


    // Only update fields that were actually filled
    $claim->date_cafoa_prepared = $request->has('date_cafoa_prepared') && $request->input('date_cafoa_prepared') !== ''
    ? $request->input('date_cafoa_prepared')
    : null;
    $claim->date_pgo_received = $request->has('date_pgo_received') && $request->input('date_pgo_received') !== ''
    ? $request->input('date_pgo_received')
    : null;
    $claim->amount_approved = $request->has('amount_approved') && $request->input('amount_approved') !== ''
    ? $request->input('amount_approved')
    : null;
    $claim->form_of_payment = $request->has('form_of_payment') && $request->input('form_of_payment') !== ''
    ? $request->input('form_of_payment')
    : null;
    $claim->payout_date = $request->has('payout_date') && $request->input('payout_date') !== ''
    ? $request->input('payout_date')
    : null;
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

    $requiresAmount = true; // Always allow amount entry

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
                'client_assistance_id' => $claim->client_assistance_id,
                'check_payment_id' => $checkPaymentId,
                'form_of_payment' => $claim->form_of_payment,
                'payout_date' => $claim->payout_date,
                'amount' => $claim->amount_approved, 
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
