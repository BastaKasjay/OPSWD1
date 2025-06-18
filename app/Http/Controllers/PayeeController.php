<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use Illuminate\Http\Request;

class PayeeController extends Controller
{
    // List all payees
    public function index()
    {
        $payees = Payee::with('client')->get();
        return response()->json($payees);
    }

    // Store a new payee
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'full_name' => 'required|string',
            'relationship' => 'required|string',
            'proof_of_relationship' => 'nullable|string',
            'updated_to_new_payee' => 'boolean',
            'previous_payee_name' => 'boolean',
        ]);

        $payee = Payee::create($validated);

        return response()->json($payee, 201);
    }

    // Show one payee
    public function show($id)
    {
        $payee = Payee::with('client')->findOrFail($id);
        return response()->json($payee);
    }

    // Update payee
    public function update(Request $request, $id)
    {
        $payee = Payee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'sometimes|string',
            'full_name' => 'sometimes|string',
            'relationship' => 'sometimes|string',
            'proof_of_relationship' => 'nullable|string',
            'updated_to_new_payee' => 'boolean',
            'previous_payee_name' => 'boolean',
        ]);

        $payee->update($validated);

        return response()->json($payee);
    }

    // Delete payee
    public function destroy($id)
    {
        $payee = Payee::findOrFail($id);
        $payee->delete();

        return response()->json(['message' => 'Payee deleted successfully']);
    }
}
