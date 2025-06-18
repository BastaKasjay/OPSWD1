<?php

namespace App\Http\Controllers;

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
            'payee_id' => 'nullable|exists:payee,payee_id',
            'date_received_request' => 'required|date',
        ]);

        ClientAssistance::create($request->all());

        return redirect()->route('client-assistance.index')->with('success', 'Client assistance added successfully.');
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
            'payee_id' => 'nullable|exists:payee,payee_id',
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
}
