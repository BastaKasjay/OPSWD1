<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AICS Quarterly Report</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <!-- Table -->
      <main class="p-3 content-wrapper">
        @if (!empty($pdf))
            {{-- PDF version --}}
            <div style="text-align: center; margin-top: 100px; margin-bottom: 50px;">
                <h1 style="font-weight: 700; font-size: 24px; color: #000; margin: 0;">
                    AICS Quarterly Reports
                </h1>
            </div>
        @else
            {{-- Web version --}}
            <h1 class="h2 fw-bold text-dark text-center mb-3">AICS Quarterly Reports</h1>
        @endif


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
                                <td>{{ $data['municipality'] }}</td>
                                <td>{{ $data['male'] }}</td>
                                <td>{{ $data['female'] }}</td>
                                <td>{{ $data['CKD'] }}</td>
                                <td>{{ $data['Cancer'] }}</td>
                                <td>{{ $data['HeartIllness'] }}</td>
                                <td>{{ $data['DiabetesHypertension'] }}</td>
                                <td>{{ $data['OtherMedical'] }}</td>
                                <!-- Regular -->
                                <td>{{ $data['RegularMedical'] }}</td>
                                <td>{{ $data['RegularBurial'] }}</td>
                                <td>{{ $data['RegularESA'] }}</td>

                                <!-- Senior -->
                                <td>{{ $data['SeniorMedical'] }}</td>
                                <td>{{ $data['SeniorBurial'] }}</td>

                                <!-- PDRRM -->
                                <td>{{ $data['PDRRMESA'] }}</td>

                                <td>{{ number_format($data['TotalAmountPaid'], 2) }}</td>
                                <td>{{ $data['unserved_clients'] }}</td>
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
                                <td>{{ $totals['CKD'] }}</td>
                                <td>{{ $totals['Cancer'] }}</td>
                                <td>{{ $totals['HeartIllness'] }}</td>
                                <td>{{ $totals['DiabetesHypertension'] }}</td>
                                <td>{{ $totals['OtherMedical'] }}</td>
                                <!-- Regular -->
                                <td>{{ number_format($totals['RegularMedical']) }}</td>
                                <td>{{ number_format($totals['RegularBurial']) }}</td>
                                <td>{{ number_format($totals['RegularESA']) }}</td>

                                <!-- Senior -->
                                <td>{{ number_format($totals['SeniorMedical']) }}</td>
                                <td>{{ number_format($totals['SeniorBurial']) }}</td>

                                <!-- PDRRM -->
                                <td>{{ number_format($totals['PDRRMESA']) }}</td>

                                <td>{{ number_format($totals['TotalAmountPaid'], 2) }}</td>
                                <td>{{ $totals['unserved_clients'] }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </main>
</body>
</html>