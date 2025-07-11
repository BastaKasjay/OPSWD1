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
            'date_received_request' => 'required|date',
        ]);

        $payee_id = \App\Models\Payee::where('client_id', $request->client_id)->value('id');

        $assistance = ClientAssistance::create([
            'client_id' => $request->client_id,
            'assistance_type_id' => $request->assistance_type_id,
            'date_received_request' => $request->date_received_request,
            'payee_id' => $payee_id,
        ]);


        \App\Models\Claim::firstOrCreate([
            'client_id' => $assistance->client_id,
            'client_assistance_id' => $assistance->id,
        ], [
            'status' => 'pending',
        ]);

        return redirect()->route('clients.assistance')->with('success', 'Client assistance added successfully.');
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
