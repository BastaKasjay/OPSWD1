<?php

namespace App\Http\Controllers;

use App\Models\ClientAssistance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientAssistance::query();

        // Get the selected year, default to current year
        $year = $request->input('year', now()->year);
        $query->whereYear('created_at', $year);

        // Filter by quarter
        $filter = $request->input('filter');

        if ($filter && str_starts_with($filter, 'quarter-')) {
            $quarter = str_replace('quarter-', '', $filter);

            $months = match($quarter) {
                '1' => [1, 2, 3],
                '2' => [4, 5, 6],
                '3' => [7, 8, 9],
                '4' => [10, 11, 12],
                default => [],
            };

            if (!empty($months)) {
                $query->whereIn(DB::raw('MONTH(created_at)'), $months);
            }
        }

        $reports = $query->get();

        return view('reports.index', compact('reports'));
    }
}
