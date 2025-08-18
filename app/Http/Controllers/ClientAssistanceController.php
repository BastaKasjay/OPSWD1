<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\ClientAssistance;
use Illuminate\Http\Request;
use App\Models\AssistanceType;

class ClientAssistanceController extends Controller
{
    public function index()
    {
        $clientAssistances = ClientAssistance::with(['client', 'assistanceType', 'payee'])->get();
        return view('client_assistance.index', compact('clientAssistances'));
    }

    public function create()
    {
        return view('client_assistance.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'assistance_category_id' => 'required|exists:assistance_categories,id',
            'date_received_request' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
        // Determine if representative is provided
        $hasRepresentative = $request->has('has_representative') 
            && $request->filled('representative_first_name') 
            && $request->filled('relationship');

        // Try to fetch existing payee
        $payee = \App\Models\Payee::where('client_id', $request->client_id)->first();

        // If no payee exists yet, create one
        if (!$payee) {
            $payee = \App\Models\Payee::create([
                'client_id' => $request->client_id,
                'first_name' => $hasRepresentative ? $request->representative_first_name : null,
                'middle_name' => $hasRepresentative ? $request->representative_middle_name : null,
                'last_name' => $hasRepresentative ? $request->representative_last_name : null,
                'contact_number' => $hasRepresentative ? $request->representative_contact_number : null,
                'relationship' => $hasRepresentative ? $request->relationship : null,
                'proof_of_relationship' => $hasRepresentative && $request->has('proof_of_relationship') ? 1 : 0,
                'full_name' => $hasRepresentative
                    ? trim($request->representative_first_name . ' ' . $request->representative_middle_name . ' ' . $request->representative_last_name)
                    : null,
                'is_self_payee' => $hasRepresentative ? 0 : 1,
            ]);
        }

            
            $othersCategory = DB::table('assistance_categories')
                ->where('assistance_type_id', $request->assistance_type_id)
                ->whereRaw('LOWER(category_name) = "others"')
                ->first();

            $assistance = ClientAssistance::create([
                'client_id' => $request->client_id,
                'assistance_type_id' => $request->assistance_type_id,
                // If user typed in custom category, force category to "Others"
                'assistance_category_id' => $request->filled('other_category_name') 
                    ? ($othersCategory ? $othersCategory->id : $request->assistance_category_id)
                    : $request->assistance_category_id,

                // Only save the custom text, never an ID
                'other_category_name' => $request->filled('other_category_name') 
                    ? $request->other_category_name 
                    : null,

                'date_received_request' => $request->date_received_request,
                'medical_case' => $request->medical_case === 'Others' && $request->filled('other_case')
                    ? $request->other_case
                    : $request->medical_case,
                'payee_id' => $payee->id,
            ]);

// dd($request->all());



            // Create claim record (status = pending by default)
            \App\Models\Claim::firstOrCreate([
                'client_id' => $assistance->client_id,
                'client_assistance_id' => $assistance->id,
            ], [
                'status' => 'pending',
            ]);
            

            DB::commit();

            return redirect()->route('clients.assistance')->with('success', 'Client assistance added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add assistance: ' . $e->getMessage());
        }

    }
    



    public function show(ClientAssistance $clientAssistance)
    {
        return view('client_assistance.show', compact('clientAssistance'));
    }

    public function edit(ClientAssistance $clientAssistance)
    {
        return view('client_assistance.edit', compact('clientAssistance'));
    }

    public function update(Request $request, ClientAssistance $clientAssistance)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'payee_id' => 'nullable|exists:payees_id',
            'date_received_request' => 'required|date',
        ]);

        $clientAssistance->update($request->all());

        return redirect()->route('client-assistance.index')->with('success', 'Client assistance updated successfully.');
    }

    public function destroy(ClientAssistance $clientAssistance)
{
    try {
        $clientAssistance->delete();
        return redirect()->back()->with('success', 'Assistance deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to delete assistance: ' . $e->getMessage());
    }
}


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,disapproved'
        ]);

        $assistance = ClientAssistance::findOrFail($id);
        $assistance->status = $request->status;
        $assistance->save();

        return back()->with('success', 'Status updated successfully!');
    }

    public function assistance(Request $request)
{
    $query = ClientAssistance::with(['client', 'assistanceType', 'payee', 'claim']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('client', function ($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('middle_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%");
        });
    }

    $assistances = $query->latest()->paginate(10);

    $assistances->getCollection()->transform(function ($assistance) {
    $claim = $assistance->claim()->latest()->first(); // get actual claim model

    if (!$claim || $claim->status === 'pending') {
        $assistance->claim_progress = 'No Claim';
    } elseif ($claim->status === 'approved') {
        // check if all required fields are filled
        $infoFilled = $claim->date_cafoa_prepared !== null
                   && $claim->date_pgo_received !== null
                   && $claim->amount_approved !== null
                   && $claim->form_of_payment !== null
                   && $claim->payout_date !== null;

        $assistance->claim_progress = $infoFilled ? 'Complete' : 'In Progress';
    } else {
        $assistance->claim_progress = 'No Claim';
    }

    return $assistance;
});


    $assistanceTypes = AssistanceType::all();

    return view('client.assistance', compact('assistances', 'assistanceTypes'));
}



}
