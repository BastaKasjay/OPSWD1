@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">AICS QUARTERLY REPORTS</h1>

    <form method="GET" action="{{ route('reports.index') }}" class="mb-4 flex flex-wrap gap-4">
        <div>
            <label for="filter" class="block text-sm font-medium text-gray-700">Quarter</label>
            <select name="filter" id="filter" class="border rounded px-2 py-1">
                <option value="">All</option>
                <option value="quarter-1" {{ request('filter') == 'quarter-1' ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                <option value="quarter-2" {{ request('filter') == 'quarter-2' ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                <option value="quarter-3" {{ request('filter') == 'quarter-3' ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                <option value="quarter-4" {{ request('filter') == 'quarter-4' ? 'selected' : '' }}>Q4 (Oct - Dec)</option>
            </select>
        </div>

        <div>
            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
            <select name="year" id="year" class="border rounded px-2 py-1">
                @foreach(range(date('Y'), 2025) as $y)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
                Filter
            </button>
        </div>
    </form>

    <div class="w-full overflow-x-auto bg-white rounded shadow p-4">
        <table border="1" cellspacing="0" cellpadding="5" class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th rowspan="3">No.</th>
                    <th rowspan="3">Municipality</th>
                    <th colspan="2" rowspan="2"># of Served Clients</th>
                    <th colspan="5" rowspan="2">Case/s</th>
                    <th colspan="6">Source of Funds</th>
                    <th rowspan="3">Amount</th>
                    <th rowspan="3"># of Unreserved Clients (Reason)<br><small>No 1 Year from the last receive</small></th>
                </tr>
                <tr>
                    <th colspan="3">Regular</th>
                    <th colspan="2">Senior Citizen</th>
                    <th>PDRRM</th>
                </tr>
                <tr>
                    <th>Male</th>
                    <th>Female</th>
                    <th>CKD</th>
                    <th>Cancer</th>
                    <th>Heart Illness</th>
                    <th>Diabetes & Hypertension</th>
                    <th>Others</th>
                    <th>Medical</th>
                    <th>Burial</th>
                    <th>ESA</th>
                    <th>Medical</th>
                    <th>Burial</th>
                    <th>ESA</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td>1</td>
                    <td>Sample Town</td>
                    <td>5</td>
                    <td>7</td>
                    <td>1</td>
                    <td>0</td>
                    <td>2</td>
                    <td>1</td>
                    <td>0</td>
                    <td>1000</td>
                    <td>2000</td>
                    <td>500</td>
                    <td>1500</td>
                    <td>1000</td>
                    <td>200</td>
                    <td>5000</td>
                    <td>1</td>
                </tr>
                <!-- Add more rows dynamically -->
            </tbody>
        </table>
    </div>
</div>
@endsection
