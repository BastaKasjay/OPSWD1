<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(){
        return view('client.index');
    }

    public function create(){
        return view('client.create');
    }

    public function docs(Request $request){
        dd($request);

    }
    
}
