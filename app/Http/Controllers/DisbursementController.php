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
            'amount'    => 'required|numeric|min:0',
            'check_no'  => 'nullable|string',
        ]);

        $claim = Claim::with('clientAssistance')->findOrFail($validated['claim_id']);

        Disbursement::create([
            'claim_id'        => $claim->id,
            'client_id'       => $claim->client_id, 
            'client_assistance_id' => $claim->client_assistance_id,
            'form_of_payment' => $claim->form_of_payment,
            'amount'          => $validated['amount'],
            'check_no'        => $validated['check_no'],
            'claim_status'    => 'unclaimed',
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

        
        return back()->with('success', 'Disbursement updated successfully.');
    }



}
