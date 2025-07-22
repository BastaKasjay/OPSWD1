@extends('layouts.app')

@section('title', 'AICS Quarterly Reports')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, html {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            height: 100vh;
        }
        .main-container {
            height: 100vh;
            overflow: hidden !important;
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
            overflow: hidden;
        }
        .table-container {
            flex: 1;
            overflow: auto;
        }
        .sticky-header th {
            position: sticky;
            top: 0;
            background-color: #e9f7ef;
        }
    </style>
@endsection

@section('content')
<div class="main-container bg-light w-100">
    <main class="p-3 content-wrapper">
        <div class="mb-4 mt-3">
            <h1 class="h2 fw-bold text-dark mb-3">AICS Quarterly Reports</h1>

            <!-- Filter Section -->
            <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="quarter" class="form-label">Quarter</label>
                    <select class="form-select" name="quarter" id="quarter">
                        <option value="">All Quarters</option>
                        <option value="Q1" {{ request('quarter') == 'Q1' ? 'selected' : '' }}>Q1</option>
                        <option value="Q2" {{ request('quarter') == 'Q2' ? 'selected' : '' }}>Q2</option>
                        <option value="Q3" {{ request('quarter') == 'Q3' ? 'selected' : '' }}>Q3</option>
                        <option value="Q4" {{ request('quarter') == 'Q4' ? 'selected' : '' }}>Q4</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" name="year" id="year">
                        @for($year = date('Y'); $year >= 2022; $year--)
                            <option value="{{ $year }}" {{ request('year', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="from" class="form-label">From (Date)</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To (Date)</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ request('to') }}">
                </div>
            </form>

        </div>

        <!-- Table -->
        <div class="bg-white p-3 rounded shadow table-container">
            <div class="table-responsive">
                <table class="table table-bordered text-center table-sm align-middle">
                    <thead style="background-color: #eef6f0; font-family: 'Inter', sans-serif;">
                        <tr>
                            <th rowspan="3" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">No.</th>
                            <th rowspan="3" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Municipality</th>
                            <th colspan="2" rowspan="2" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;"># of Served Clients</th>
                            <th colspan="5" rowspan="2" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Case/s</th>
                            <th colspan="6" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Source of Funds</th>
                            <th rowspan="3" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Amount</th>
                            <th rowspan="3" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;"># of Unserved</th>
                        </tr>
                        <tr>
                            <th colspan="3" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Regular</th>
                            <th colspan="2" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">Senior Citizen</th>
                            <th colspan="1" style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0; text-transform: uppercase;">PDRRM</th>
                        </tr>
                        <tr>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Male</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Female</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">CKD</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Cancer</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Heart Illness</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Diabetes & Hypertension</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Others</th>

                            <!-- Regular -->
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Medical</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Burial</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">ESA</th>

                            <!-- Senior Citizen -->
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Medical</th>
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">Burial</th>

                            <!-- PDRRM -->
                            <th style="color: #374151; font-size: 1rem; font-weight: 600; background-color: #eef6f0;">ESA</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->municipality }}</td>
                                <td>{{ $data->male }}</td>
                                <td>{{ $data->female }}</td>
                                <td>{{ $data->CKD }}</td>
                                <td>{{ $data->Cancer }}</td>
                                <td>{{ $data->HeartIllness }}</td>
                                <td>{{ $data->DiabetesHypertension }}</td>
                                <td>{{ $data->OtherMedical }}</td>
                                <!-- Regular -->
                                <td>{{ $data->RegularMedical }}</td>
                                <td>{{ $data->RegularBurial }}</td>
                                <td>{{ $data->RegularESA }}</td>

                                <!-- Senior -->
                                <td>{{ $data->SeniorMedical }}</td>
                                <td>{{ $data->SeniorBurial }}</td>

                                <!-- PDRRM -->
                                <td>{{ $data->PDRRMESA }}</td>

                                <td>&#8369;{{ number_format($data->TotalAmountPaid, 2) }}</td>
                                <td>{{ $data->unserved_clients }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-muted text-center py-3">No data available for the selected period</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($reportData) > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td>{{ $totals['male'] }}</td>
                                <td>{{ $totals['female'] }}</td>
                                <td>{{ $totals['ckd'] }}</td>
                                <td>{{ $totals['cancer'] }}</td>
                                <td>{{ $totals['heart_illness'] }}</td>
                                <td>{{ $totals['diabetes_hypertension'] }}</td>
                                <td>{{ $totals['others'] }}</td>
                                <!-- Regular -->
                                <td>{{ number_format($totals['regular_medical']) }}</td>
                                <td>{{ number_format($totals['regular_burial']) }}</td>
                                <td>{{ number_format($totals['regular_esa']) }}</td>

                                <!-- Senior -->
                                <td>{{ number_format($totals['senior_medical']) }}</td>
                                <td>{{ number_format($totals['senior_burial']) }}</td>

                                <!-- PDRRM -->
                                <td>{{ number_format($totals['pdrrm_esa']) }}</td>

                                <td>&#8369;{{ number_format($totals['amount_total'], 2) }}</td>
                                <td>{{ $totals['unreserved'] }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
