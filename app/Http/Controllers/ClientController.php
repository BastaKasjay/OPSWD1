<?php

namespace App\Http\Controllers;

use App\Models\VulnerabilitySector;
use App\Models\Client;
use App\Models\Municipality;
use App\Models\AssistanceType;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('municipality')->get();
        return view('client.index', compact('clients'));
    }

    public function create()
    {
        $municipalities = Municipality::all();
        $vulnerabilitySectors = VulnerabilitySector::all();
        $assistanceTypes = AssistanceType::all();
        return view('client.create', compact('municipalities', 'vulnerabilitySectors', 'assistanceTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'municipality_id' => 'required|exists:municipalities,id',
            'sex' => 'required',
            'age' => 'required|integer',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'vulnerability_sectors' => 'array|nullable',
        ]);

        $client = Client::create($request->except('vulnerability_sectors'));

        if ($request->has('vulnerability_sectors')) {
            $client->vulnerabilitySectors()->attach($request->vulnerability_sectors);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
    }
}
