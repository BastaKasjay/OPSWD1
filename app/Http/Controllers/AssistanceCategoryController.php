<?php

namespace App\Http\Controllers;

use App\Models\AssistanceCategory;
use Illuminate\Http\Request;

class AssistanceCategoryController extends Controller
{
    public function index()
    {
        $categories = AssistanceCategory::all();
        return view('assistance_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('assistance_categories.create');
    }

    public function store(Request $request)
    {
        AssistanceCategory::create($request->validate([
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('assistance_categories.index')->with('success', 'Category created successfully.');
    }

    public function show(AssistanceCategory $assistanceCategory)
    {
        return view('assistance_categories.show', compact('assistanceCategory'));
    }

    public function edit(AssistanceCategory $assistanceCategory)
    {
        return view('assistance_categories.edit', compact('assistanceCategory'));
    }

    public function update(Request $request, AssistanceCategory $assistanceCategory)
    {
        $assistanceCategory->update($request->validate([
            'assistance_type_id' => 'required|exists:assistance_types,id',
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('assistance_categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(AssistanceCategory $assistanceCategory)
    {
        $assistanceCategory->delete();
        return redirect()->route('assistance_categories.index')->with('success', 'Category deleted successfully.');
    }
}
