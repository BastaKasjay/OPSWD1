<?php

namespace App\Http\Controllers;

use App\Models\Disbursement;
use App\Models\Claim;
use Illuminate\Http\Request;

class DisbursementController extends Controller
{

    public function index()
    {
        $disbursements = Disbursement::with(['client', 'claim'])
            ->latest()
            ->paginate(20);

        return view('disbursements.index', compact('disbursements'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'claim_id'  => 'required|exists:claims,id',
            'client_id' => 'required|exists:clients,id',
            'amount'    => 'required|numeric|min:0',
            'check_no'  => 'nullable|string',
        ]);

        // Fetch the form_of_payment from the related claim
        $claim = Claim::findOrFail($validated['claim_id']);

        Disbursement::create([
            'claim_id'         => $validated['claim_id'],
            'client_id'        => $validated['client_id'],
            'form_of_payment'  => $claim->form_of_payment, // This is now from the claim
            'amount'           => $validated['amount'],
            'check_no'         => $validated['check_no'],
            'claim_status'     => 'unclaimed',
        ]);

        return back()->with('success', 'Disbursement created.');
    }



    public function updateClaimStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'claim_status' => 'required|in:claimed,unclaimed,pending',
            'date_received_claimed' => 'nullable|date',
            'date_released' => 'nullable|date',
            'total_amount_claimed' => 'nullable|numeric|min:0',
        ]);

        $disbursement = Disbursement::findOrFail($id);


        $disbursement->update([
            'claim_status' => $validated['claim_status'],
            'date_received_claimed' => $validated['date_received_claimed'],
            'date_released' => $validated['date_released'],
            'total_amount_claimed' => $validated['total_amount_claimed'],
        ]);

        // Send back modal ID to show again (if needed)
        return back()->with('success', 'Disbursement updated successfully.');
    }



}
