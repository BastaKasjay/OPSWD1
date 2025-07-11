<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Municipality;
use App\Models\VulnerabilitySector;
use App\Models\AssistanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['municipality', 'vulnerabilitySectors', 'assistanceType', 'assistanceCategory']);

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

        return view('client.index', compact('clients', 'municipalities', 'vulnerabilityCounts', 'totalVulnerable'));
    }

    public function show($id)
    {
        $client = Client::with([
            'municipality',
            'vulnerabilitySectors',
            'assistanceType',
            'assistanceCategory'
        ])->findOrFail($id);

        return view('client.show', compact('client'));
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
            'contact_number' => 'required|string',
            'representative_first_name' => 'nullable|string',
            'representative_middle_name' => 'nullable|string',
            'representative_last_name' => 'nullable|string',
            'representative_contact_number' => 'nullable|string',
            'municipality_id' => 'required|exists:municipalities,id',
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'assistance_category_id' => 'required|exists:assistance_categories,id',
            'vulnerability_sectors' => 'array|nullable'
        ]);

        $client = Client::create($validated);

        if ($request->has('vulnerability_sectors')) {
            $client->vulnerabilitySectors()->attach($request->vulnerability_sectors);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }

    public function edit($id)
    {
        $client = Client::with(['vulnerabilitySectors'])->findOrFail($id);

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
        $validated = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'sex' => 'required',
            'age' => 'required|integer',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'representative_first_name' => 'nullable|string',
            'representative_middle_name' => 'nullable|string',
            'representative_last_name' => 'nullable|string',
            'representative_contact_number' => 'nullable|string',
            'municipality_id' => 'required|exists:municipalities,id',
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'assistance_category_id' => 'required|exists:assistance_categories,id',
            'vulnerability_sectors' => 'array|nullable'
        ]);

        $client = Client::findOrFail($id);
        $client->update($validated);

        // Sync vulnerability sectors
        $client->vulnerabilitySectors()->sync($request->vulnerability_sectors ?? []);

        // ✅ Redirect to show page after successful update
        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client updated successfully');
    }
}