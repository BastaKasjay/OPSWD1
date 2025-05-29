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
            //patient information
            'name' => $request->input('name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'sex' => $request->input('sex'),
            'age' => $request->input('age'),
            'pwd' => $request->input('pwd'),
            '_4ps' => $request->input('4ps'),
            'address' => $request->input('address'),
            'contact_number' => $request->input('contact_number'),
            'valid_id' => $request->input('valid_id'),
            'municipality' => $municipality ? $municipality ->name : 'Not Found',
            'assistance_type' => $request->input('assistance_type'),
            'assistance_category' => $request->input('assistance_category'),

            //client information
            'client_name' => $request->input('client_name'),
            'client_middle_name' => $request->input('client_middle_name'),
            'client_last_name' => $request->input('client_last_name'),
            'client_relationship' => $request->input('client_relationship'),


        ]);
    }
    
}
