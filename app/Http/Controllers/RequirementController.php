<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::all();
        return view('requirements.index', compact('requirements'));
    }

    public function create()
    {
        return view('requirements.create');
    }

    public function store(Request $request)
    {
        Requirement::create($request->validate([
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('requirements.index')->with('success', 'Requirement created successfully.');
    }

    public function show(Requirement $requirement)
    {
        return view('requirements.show', compact('requirement'));
    }

    public function edit(Requirement $requirement)
    {
        return view('requirements.edit', compact('requirement'));
    }

    public function update(Request $request, Requirement $requirement)
    {
        $requirement->update($request->validate([
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('requirements.index')->with('success', 'Requirement updated successfully.');
    }

    public function destroy(Requirement $requirement)
    {
        $requirement->delete();
        return redirect()->route('requirements.index')->with('success', 'Requirement deleted successfully.');
    }
}
