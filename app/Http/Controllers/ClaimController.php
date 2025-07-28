<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\Client;
use App\Models\ClientAssistance;

class ClaimController extends Controller
{
    public function updateStatus(Request $request, $clientId)
    {
        $status = $request->input('status');

        try {
            // Get the client_assistance record
            $clientAssistance = ClientAssistance::where('client_id', $clientId)->first();

            if (!$clientAssistance) {
                return redirect()->back()->with('error', 'No assistance record found for this client.');
            }

            // Find or create the claim record
            $claim = Claim::firstOrCreate(
                ['client_assistance_id' => $clientAssistance->id],
                ['client_id' => $clientId]
            );

            // dd($claim); // Debug the claim object

            // Update the status
            $claim->status = (string) $status;
            $claim->save();

            return redirect()->back()->with('success', 'Status updated to ' . ucfirst($status) . '.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());

        }
    }


    public function update(Request $request, $id)
{
    $request->validate([
        'form_of_payment' => 'required|in:cash,cheque', // ✅ Must be required
        'check_no' => 'required_if:form_of_payment,cheque',
        'payout_date' => 'required|date', // ✅ Must be required
    ]);

    $claim = Claim::with('client', 'disbursement')->findOrFail($id);

    // ✅ Update claim details
    $claim->date_cafoa_prepared = $request->input('date_cafoa_prepared');
    $claim->date_pgo_received = $request->input('date_pgo_received');
    $claim->amount_approved = $request->input('amount_approved');
    $claim->form_of_payment = $request->input('form_of_payment');
    $claim->payout_date = $request->input('payout_date');
    $claim->reason_of_disapprovement = $request->input('reason_of_disapprovement');
    $claim->save();

    $cashPaymentId = null;
    $checkPaymentId = null;

    // ✅ Create/update CashPayment or CheckPayment
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

    } elseif ($claim->form_of_payment === 'cheque') {
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

    // ✅ Always create/update disbursement (payout_date & form_of_payment now always exist)
    \App\Models\Disbursement::updateOrCreate(
        ['claim_id' => $claim->id],
        [
            'client_id' => $claim->client_id,
            'cash_payment_id' => $cashPaymentId,
            'check_payment_id' => $checkPaymentId,
            'form_of_payment' => $claim->form_of_payment,
            'payout_date' => $claim->payout_date,
            'amount' => $claim->amount_approved,
            'claim_status' => $claim->disbursement->claim_status ?? 'pending',
            'date_received_claimed' => $claim->disbursement->date_received_claimed ?? null,
            'date_released' => $claim->disbursement->date_released ?? null,
            'total_amount_claimed' => $claim->disbursement->total_amount_claimed ?? null,
        ]
    );

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
