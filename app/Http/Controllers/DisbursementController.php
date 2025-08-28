<?php

namespace App\Http\Controllers;

use App\Models\Disbursement;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DisbursementController extends Controller
{


    public function index()
    {
        $disbursements = Disbursement::with(['client', 'claim', 'checkPayment'])
    ->orderByRaw("
        CASE 
            WHEN claim_status = 'pending' THEN 0 
            WHEN claim_status = 'unclaimed' THEN 1
            WHEN claim_status = 'claimed' THEN 2
            ELSE 3
        END
    ")
    ->orderBy('created_at', 'desc')
    ->paginate(20);

        return view('claims.grouped', compact('disbursements'));
    }




    public function store(Request $request)
{
    $validated = $request->validate([
        'claim_id' => 'required|exists:claims,id',
        'amount' => 'required|numeric|min:0',
        'date_received_claimed' => 'nullable|date',
        'claim_status' => 'required|in:claimed,unclaimed', // add statuses
    ]);

    $claim = Claim::with('clientAssistance')->findOrFail($validated['claim_id']);
    $claim->amount = $validated['amount'];

    // Enforce logic: if date_received_claimed is set, claim_status must be claimed
    if ($request->date_received_claimed && $request->claim_status !== 'claimed') {
        return back()->withErrors([
            'claim_status' => 'You must mark the claim as "claimed" if you enter a received date.'
        ])->withInput();
    }

    // Auto-set date_received_claimed if claim_status is claimed but date is empty
    $dateReceived = $request->date_received_claimed;
    if ($request->claim_status === 'claimed' && !$dateReceived) {
        $dateReceived = now()->format('Y-m-d');
    }

    Disbursement::create([
        'claim_id' => $claim->id,
        'client_id' => $claim->client_id,
        'client_assistance_id' => $claim->client_assistance_id,
        'form_of_payment' => $claim->form_of_payment,
        'amount' => $claim->amount,
        'claim_status' => $request->claim_status,
        'date_received_claimed' => $dateReceived,
    ]);

    if (strtolower($claim->form_of_payment) === 'cheque' && $claim->checkPayment) {
        $claim->checkPayment()->create([
            'check_number' => $claim->checkPayment->check_no,
            // Add other required fields
        ]);
    }

    return back()->with('success', 'Disbursement created.');
}




    public function updateClaimStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'claim_status' => 'required|in:claimed,unclaimed,pending',
            'date_received_claimed' => 'nullable|date',
            
        ]);

        $disbursement = Disbursement::findOrFail($id);

        if ($validated['claim_status'] === 'claimed' && empty($validated['date_received_claimed'])) {
        $validated['date_received_claimed'] = now();
    }


        $disbursement->update([
            'claim_status' => $validated['claim_status'],
            'date_received_claimed' => $validated['date_received_claimed'],
            
        ]);

        // Preserve filter after update
        $payoutDate = $request->input('payout_date');

        return redirect()->route('claims.grouped', [
            'payout_date' => $payoutDate !== 'all' ? $payoutDate : null,
        ])->with('success', 'Disbursement updated successfully.');

    }

    public function batchUpdate(Request $request)
    {
        $ids = explode(',', $request->input('selected_ids'));
        $data = $request->only(['date_received_claimed', 'claim_status']);

        // Filter out empty values so we don't overwrite existing data unintentionally
        $data = array_filter($data, fn($v) => !is_null($v) && $v !== '');

        if (empty($data)) {
            return redirect()->back()->with('error', 'No fields to update.');
        }

        Disbursement::whereIn('claim_id', $ids)->update($data);

        return redirect()->route('claims.grouped')->with('success', 'Disbursement updated successfully.');

    }


    public function getClaimStatuses(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['sameStatus' => false]);
        }

        $statuses = Disbursement::whereIn('id', $ids)->pluck('claim_status')->unique();

        if ($statuses->count() === 1) {
            return response()->json([
                'sameStatus' => true,
                'status' => $statuses->first(), // Example: "Unclaimed"
            ]);
        }

        return response()->json([
            'sameStatus' => false,
            'status' => null,
        ]);
    }




}
