<?php

namespace App\Http\Controllers;

use App\Models\ClientAssistance;
use App\Models\Requirement;
use App\Models\AssistanceCategory;
use Illuminate\Http\Request;

class AssistanceController extends Controller
{
    public function getRequirements($id)
    {
        return response()->json(
        Requirement::where('assistance_type_id', $id)->get(['id', 'requirement_name'])
        );
    }

    public function getCategories($id)
    {
        return response()->json(
        AssistanceCategory::where('assistance_type_id', $id)->get(['id', 'category_name'])
        );
    }

    public function assistance()
    {
        $assistances = \App\Models\ClientAssistance::with([
            'client.municipality',
            'assistanceType',
            'assistanceCategory'
        ])->paginate(10); // Use paginate only if you want pagination

        return view('client.assistance', compact('assistances')); // Points to resources/views/client/assistance.blade.php
    }


    public function edit($id)
    {
        $assistance = ClientAssistance::with(['client', 'assistanceType', 'assistanceCategory'])->findOrFail($id);
        // other logic as needed
        return view('client.edit', compact('assistance'));
    }

}
