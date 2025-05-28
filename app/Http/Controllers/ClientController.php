<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipality;

class ClientController extends Controller
{
    public function index(){
        return view('client.index');
    }

    public function create(){
        $municipalities = Municipality::all();
        return view('client.create', compact('municipalities'));
    }

    public function docs(Request $request){
        $municipalityId = $request->input('municipality_id');
        $municipality = Municipality::find($municipalityId);

        return view('client.docs', [
            'name' => $request->input('name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'relationship' => $request->input('relationship'),
            'sex' => $request->input('sex'),
            'age' => $request->input('age'),
            '4ps' => $request->input('4ps') ? true : false,
            'pwd' => $request->input('pwd') ? true : false,
            'address' => $request->input('address'),
            'contact_number' => $request->input('contact_number'),
            'valid_id' => $request->input('valid_id'),
            'municipality' => $municipality ? $municipality ->name : 'Not Found',
            'assistance_type' => $request->input('assistance_type'),
            'assistance_category' => $request->input('assistance_category'),
        ]);
    }
    
}
