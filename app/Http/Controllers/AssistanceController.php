<?php

namespace App\Http\Controllers;

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
}
