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
        $disbursements = Disbursement::with(['client', 'claim'])
            ->latest()
            ->paginate(20);

        return view('claims.grouped', compact('disbursements'));
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'claim_id'  => 'required|exists:claims,id',
            'amount'    => 'required|numeric|min:0',
            'check_no'  => 'nullable|string',
        ]);

        $claim = Claim::with('clientAssistance')->findOrFail($validated['claim_id']);
        $claim->amount = $validated['amount'];

        Disbursement::create([
            'claim_id'        => $claim->id,
            'client_id'       => $claim->client_id, 
            'client_assistance_id' => $claim->client_assistance_id,
            'form_of_payment' => $claim->form_of_payment,
            'amount'          => $claim->amount,
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
            
        ]);

        $disbursement = Disbursement::findOrFail($id);


        $disbursement->update([
            'claim_status' => $validated['claim_status'],
            'date_received_claimed' => $validated['date_received_claimed'],
            'date_released' => $validated['date_released'],
            
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
        $data = $request->only(['date_received_claimed', 'date_released', 'claim_status']);

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
