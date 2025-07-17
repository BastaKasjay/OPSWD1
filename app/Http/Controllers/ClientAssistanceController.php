<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\ClientAssistance;
use Illuminate\Http\Request;

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
            // Try to fetch existing payee
            $payee = \App\Models\Payee::where('client_id', $request->client_id)->first();

            // If no payee exists yet, create one (either representative or self-payee)
            if (!$payee) {
                $hasRepresentative = $request->has('has_representative') && $request->filled('representative_first_name') && $request->filled('relationship');

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

            // Create client assistance
            $assistance = ClientAssistance::create([
                'client_id' => $request->client_id,
                'assistance_type_id' => $request->assistance_type_id,
                'assistance_category_id' => $request->assistance_category_id,
                'date_received_request' => $request->date_received_request,
                'medical_case' => $request->medical_case,
                'payee_id' => $payee->id,
            ]);

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
        $clientAssistance->delete();
        return redirect()->route('client-assistance.index')->with('success', 'Client assistance deleted.');
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


}
