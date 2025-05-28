<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    public function show(Request $request)
    {
        $municipalityId = $request->input('municipality_id');
        // Do something with $municipalityId
        return $municipalityId;
    }
}