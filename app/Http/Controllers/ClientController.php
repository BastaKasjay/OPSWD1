<?php

namespace App\Http\Controllers;

use App\Models\ClientAssistance;
use App\Models\Client;
use App\Models\Municipality;
use App\Models\VulnerabilitySector;
use App\Models\AssistanceType;
use App\Models\AssistanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['municipality', 'vulnerabilitySectors', 'payee']);

        if ($request->municipality_id) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->get();
        $municipalities = Municipality::all();
        $clientIds = $clients->pluck('id')->toArray();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::all();
        $assistanceCategories = AssistanceCategory::all();


        // Vulnerability sectors count per sector
        $vulnerabilityCounts = VulnerabilitySector::withCount([
            'clients as clients_count' => function ($q) use ($clientIds) {
                $q->whereIn('client_id', $clientIds);
            }
        ])->get();

        // Total count for 'All'
        $totalVulnerable = DB::table('client_vulnerability_sector')
            ->whereIn('client_id', $clientIds)
            ->count();

        return view('client.index', compact('clients', 'municipalities', 'assistanceTypes', 'assistanceCategories', 'vulnerabilitySectors', 'vulnerabilityCounts', 'totalVulnerable'));
    }

    public function show($id)
    {
        $client = Client::with([
            'municipality',
            'vulnerabilitySectors',
            'payee',
            'assistances.assistanceType',
            'assistances.assistanceCategory',
        ])->findOrFail($id);

        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::all();
        $assistanceCategories = AssistanceCategory::all();

        return view('client.show', compact(
            'client',
            'municipalities',
            'vulnerabilitySectors',
            'assistanceTypes',
            'assistanceCategories'
        ));
    }


    public function create()
    {
        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::with('categories')->get();

        return view('client.create', compact('municipalities', 'vulnerabilitySectors', 'assistanceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'sex' => 'required',
            'age' => 'required|integer',
            'address' => 'required|string',
            'contact_number' => 'nullable|string',
            'birthday' => 'nullable|date',
            'municipality_id' => 'required|exists:municipalities,id',
            'vulnerability_sectors' => 'array|nullable'
        ]);

        DB::beginTransaction();

        try {
            $client = Client::create($validated);

            if ($request->has('vulnerability_sectors')) {
                $client->vulnerabilitySectors()->attach($request->vulnerability_sectors);
            }

            DB::commit();
            return redirect()->route('clients.index')->with('success', 'Client created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong while saving client: ' . $e->getMessage());
        }
    }





    public function edit($id)
    {
        $client = Client::with(['vulnerabilitySectors', 'payee'])->findOrFail($id);

        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        
        // 🟢 The important part: eager load categories for all assistance types
        $assistanceTypes = AssistanceType::with('categories')->get();

        // 🟢 Get the categories based on the client's selected assistance type
        $assistanceCategories = $client->assistance_type_id 
            ? AssistanceType::find($client->assistance_type_id)->categories 
            : collect();

        return view('client.edit', compact(
            'client',
            'municipalities',
            'vulnerabilitySectors',
            'assistanceTypes',
            'assistanceCategories'
        ));
    }





    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'sex' => 'required',
            'age' => 'required|integer',
            'address' => 'required|string',
            'contact_number' => 'nullable|string',
            'birthday' => 'nullable|date',
            'municipality_id' => 'required|exists:municipalities,id',
            'representative_first_name' => 'nullable|string',
            'representative_middle_name' => 'nullable|string',
            'representative_last_name' => 'nullable|string',
            'representative_contact_number' => 'nullable|string',
            'relationship' => 'nullable|string',
            'proof_of_relationship' => 'nullable|boolean',
            'vulnerability_sectors' => 'array|nullable',
            'assistanceTypes' => 'array|nullable',
            'assistanceCategories' => 'array|nullable'
        ]);

        $clientData = $request->only([
            'first_name', 'middle_name', 'last_name',
            'sex', 'age', 'address', 'contact_number',
            'birthday', 'municipality_id'
        ]);
        $client->update($clientData);

        // Sync vulnerability sectors
        $client->vulnerabilitySectors()->sync($request->vulnerability_sectors ?? []);

        // Handle payee
        $hasRepresentative = $request->has('has_representative');

        $payeeData = [
            'client_id' => $client->id,
            'first_name' => $hasRepresentative ? $request->representative_first_name : null,
            'middle_name' => $hasRepresentative ? $request->representative_middle_name : null,
            'last_name' => $hasRepresentative ? $request->representative_last_name : null,
            'full_name' => $hasRepresentative
                ? trim($request->representative_first_name . ' ' . $request->representative_middle_name . ' ' . $request->representative_last_name)
                : null,
            'relationship' => $hasRepresentative && $request->filled('relationship') ? $request->relationship : null,
            'contact_number' => $hasRepresentative ? $request->representative_contact_number : null,
            'proof_of_relationship' => $hasRepresentative && $request->has('proof_of_relationship') ? 1 : 0,
            'is_self_payee' => $hasRepresentative ? 0 : 1,
        ];

        \App\Models\Payee::updateOrCreate(
            ['client_id' => $client->id],
            $payeeData
        );

        // Update assistance type/category/medical_case for the first assistance record
        if ($client->assistances()->exists()) {
            $assistance = $client->assistances()->first();
            $assistance->update([
                'assistance_type_id' => $request->input('assistance_type_id'),
                'assistance_category_id' => $request->input('assistance_category_id'),
                'medical_case' => $request->input('medical_case'),
            ]);
        }

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client updated successfully');
    }


    public function assistancesView(Request $request)
    {
        $query = Client::whereHas('assistances')
        ->with([
            'municipality',
            'vulnerabilitySectors',
            'assistances.assistanceType',
            'assistances.assistanceCategory',
            'assistances.payee'
        ]);


        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        } else {

            $query->whereHas('assistances');
        }

        $clients = $query->get();
        $assistanceTypes = AssistanceType::all(); 

        return view('client.assistance', compact('clients', 'assistanceTypes')); 
    }

    public function searchClients(Request $request)
    {
        $query = $request->get('q');

        $clients = Client::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('middle_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->with('municipality')
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'first_name' => $client->first_name,
                    'middle_name' => $client->middle_name,
                    'last_name' => $client->last_name,
                    'municipality' => $client->municipality->name ?? 'N/A',
                ];
            });

        return response()->json($clients);
    }




}