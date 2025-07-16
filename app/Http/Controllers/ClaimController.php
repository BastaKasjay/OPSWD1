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
            'form_of_payment' => 'nullable|in:cash,cheque',
            'check_no' => 'required_if:form_of_payment,cheque',
        ]);

        $claim = Claim::with('client', 'disbursement')->findOrFail($id);

        $claim->date_cafoa_prepared = $request->input('date_cafoa_prepared');
        $claim->date_pgo_received = $request->input('date_pgo_received');
        $claim->date_pto_received = $request->input('date_pto_received');
        $claim->amount_approved = $request->input('amount_approved');
        $claim->form_of_payment = $request->input('form_of_payment');
        $claim->confirmation = $request->input('confirmation');
        $claim->reason_of_disapprovement = $request->input('reason_of_disapprovement');
        $claim->save();

        if (
            $claim->status === 'approved' &&
            $claim->confirmation &&
            $claim->form_of_payment
        ) {
            $client = $claim->client;
            $representative = $client?->payee;
            $representativeName = $representative && !$representative->is_self_payee
                ? $representative->full_name
                : $client?->full_name;

            $cashPaymentId = null;
            $checkPaymentId = null;

            if ($claim->form_of_payment === 'cash') {
                $cashPayment = \App\Models\CashPayment::updateOrCreate(
                    ['claim_id' => $claim->id],
                    [
                        'client_id' => $claim->client_id,
                        'date_prepared' => now(),
                        'confirmed_people' => [$representativeName],
                        'amount_confirmed' => $claim->amount_approved,
                        'total_amount_withdrawn' => $claim->amount_approved,
                        'date_of_payout' => $claim->confirmation,
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
                        'date_of_payout' => $claim->confirmation,
                    ]
                );

                $checkPaymentId = $checkPayment->id;
                \App\Models\CashPayment::where('claim_id', $claim->id)->delete();
            }

            // ✅ Always update or create the disbursement with full synced data
            \App\Models\Disbursement::updateOrCreate(
                ['claim_id' => $claim->id],
                [
                    'client_id' => $claim->client_id,
                    'cash_payment_id' => $cashPaymentId,
                    'check_payment_id' => $checkPaymentId,
                    'form_of_payment' => $claim->form_of_payment,
                    'confirmation_date' => $claim->confirmation,
                    'amount' => $claim->amount_approved,
                    'claim_status' => 'pending', // default initial status
                    'date_received_claimed' => null,
                    'date_released' => null,
                    'total_amount_claimed' => null,
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
            ->whereNotNull('confirmation')
            ->whereNotNull('form_of_payment')
            ->get();

        // Group by confirmation date and form of payment
        $grouped = $claims->groupBy([
            fn($item) => $item->confirmation,
            fn($item) => $item->form_of_payment
        ]);

        return view('claims.grouped', compact('grouped'));
    }


}
