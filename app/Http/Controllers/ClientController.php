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

        $clients = $query->paginate(15);
        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::all();
        $assistanceCategories = AssistanceCategory::all();

        return view('client.index', compact(
            'clients',
            'municipalities',
            'assistanceTypes',
            'assistanceCategories',
            'vulnerabilitySectors'
        ));
    }

    public function assistancesIndex(Request $request)
{
    $query = \App\Models\ClientAssistance::with([
        'client.municipality',
        'client.vulnerabilitySectors',
        'assistanceType',
        'assistanceCategory',
        'payee'
    ]);

    if ($request->municipality_id) {
        $query->whereHas('client', function ($q) use ($request) {
            $q->where('municipality_id', $request->municipality_id);
        });
    }

    if ($request->search) {
        $query->whereHas('client', function ($q) use ($request) {
            $q->where('first_name', 'like', "%{$request->search}%")
              ->orWhere('middle_name', 'like', "%{$request->search}%")
              ->orWhere('last_name', 'like', "%{$request->search}%");
        });
    }

    // ✅ Show newest assistance first
    $assistances = $query->latest()->paginate(10);

    $municipalities = Municipality::all();
    $vulnerabilitySectors = VulnerabilitySector::all();
    $assistanceTypes = AssistanceType::all();
    $assistanceCategories = AssistanceCategory::all();

    return view('client.assistance', compact(
        'assistances',
        'municipalities',
        'assistanceTypes',
        'assistanceCategories',
        'vulnerabilitySectors'
    ));
}




    public function show($id)
    {
        $client = Client::with([
            'municipality',
            'vulnerabilitySectors',
            'payee',
        ])->findOrFail($id);

        // ✅ Get latest assistance based on date_received_request
        $latestAssistance = $client->assistances()
            ->with(['assistanceType', 'assistanceCategory'])
            ->latest('date_received_request')
            ->first();

        // ✅ Get claim for that latest assistance
        $claim = $latestAssistance
            ? \App\Models\Claim::where('client_assistance_id', $latestAssistance->id)->first()
            : null;

        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::all();
        $assistanceCategories = AssistanceCategory::all();

        return view('client.show', compact(
            'client',
            'municipalities',
            'vulnerabilitySectors',
            'assistanceTypes',
            'assistanceCategories',
            'latestAssistance',
            'claim'
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
            'valid_id' => 'boolean',
            'vulnerability_sectors' => 'array|nullable'
        ]);

        $validated['valid_id'] = $request->has('valid_id');

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
            'valid_id' => 'boolean',
        ]);

        $clientData = $request->only([
            'first_name', 'middle_name', 'last_name',
            'sex', 'age', 'address', 'contact_number',
            'birthday', 'municipality_id'
        ]);
        $clientData['valid_id'] = $request->has('valid_id');

        $client->update($clientData);

        // Sync vulnerability sectors
        $client->vulnerabilitySectors()->sync($request->vulnerability_sectors ?? []);

        return redirect()->route('clients.index', $client->id)
            ->with('success', 'Client updated successfully');
    }

    public function updateAssistance(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $assistance = $client->assistances()->firstOrFail(); // Adjust if multiple assistance records are possible

        $request->validate([
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'assistance_category_id' => 'required|exists:assistance_categories,id',
            'medical_case' => 'nullable|string',
            'other_case' => 'nullable|string',
            'representative_first_name' => 'nullable|string',
            'representative_middle_name' => 'nullable|string',
            'representative_last_name' => 'nullable|string',
            'representative_contact_number' => 'nullable|string',
            'relationship' => 'nullable|string',
            'proof_of_relationship' => 'nullable|boolean',
        ]);

        $assistance->update([
            'assistance_type_id' => $request->assistance_type_id,
            'assistance_category_id' => $request->assistance_category_id,
            'medical_case' => $request->medical_case === 'Others'
                ? $request->other_case
                : $request->medical_case,
            'assessed_by' => $request->assessed_by
        ]);
            // Update Payee (Representative)
        $hasRepresentative = $request->has('has_representative');

        \App\Models\Payee::updateOrCreate(
            ['client_id' => $client->id],
            [
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
            ]
        );

        return redirect()->route('clients.show', $client->id)->with('success', 'Assistance info updated.');
    }




    public function assistancesView(Request $request)
{
    $query = ClientAssistance::with([
        'client.municipality',
        'client.vulnerabilitySectors',
        'assistanceType',
        'assistanceCategory',
        'payee'
    ])->latest(); 

    if ($request->search) {
        $query->whereHas('client', function ($q) use ($request) {
            $q->where('first_name', 'like', '%' . $request->search . '%')
              ->orWhere('middle_name', 'like', '%' . $request->search . '%')
              ->orWhere('last_name', 'like', '%' . $request->search . '%');
        });
    }

    $assistances = $query->latest()->paginate(10);
    $assistanceTypes = AssistanceType::all();

    return view('client.assistance', compact('assistances', 'assistanceTypes'));
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