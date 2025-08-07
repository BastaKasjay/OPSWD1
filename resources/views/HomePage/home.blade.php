@extends('layouts.app')
@section('title', 'Home')

@section('content')

    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
            <div class="ms-auto">
                <button class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#budgetModal">
                    <i class="fas fa-coins"></i> Manage Budget
                </button>
            </div>

            <div class="modal fade" id="budgetModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Annual Budget</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('budgets.store') }}" method="POST">
                            @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Year</label>
                                <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fund Type</label>
                                <select name="type" class="form-select" required>
                                <option value="">-- Select --</option>
                                <option value="Regular">Regular</option>
                                <option value="Senior">Senior Citizen</option>
                                <option value="PDRRM">PDRRM</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Allocated Amount</label>
                                <input type="number" step="0.01" name="allocated_amount" class="form-control" required>
                            </div>
                        </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success w-100">Save Budget</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </header>

        <!-- Dashboard Cards -->
        <div class="row">
            
            <div class="col-md-3">
                <div class="dashboard-card card-purple">
                    <div class="card-title">Claimed Payouts</div>
                    <div class="card-value">{{ $totalPayouts }}</div>

                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-orange">
                    <div class="card-title">Total Clients</div>
                    <div class="card-value">{{ $totalClients }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-green">
                    <div class="card-title">Disbursed Amount</div>
                    <div class="card-value">
                        <ul class="list-unstyled m-0" style="font-size: 14px; line-height: 1.5;">
                            @foreach (['Regular', 'Senior', 'PDRRM'] as $type)
                                @php
                                    $budget = $budgets->firstWhere('type', $type)?->allocated_amount ?? 0;
                                    $disbursed = $disbursedPerType[$type] ?? 0;
                                @endphp
                                <li>
                                    <strong>{{ $type === 'Senior' ? 'Senior Citizen' : $type }}:</strong>
                                    ₱{{ number_format($disbursed, 2) }} / ₱{{ number_format($budget, 2) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="dashboard-card card-red">
                    <div class="card-title">Unserved Clients</div>
                   <div class="card-value">{{ $unservedPercentage }} %</div>

                </div>
            </div>
        </div>

        <!-- Served Clients, Upcoming Payouts, Scheduled Payouts, Previous Payouts -->
        <div class="row mt-4">
            <!-- Left Column (Served Clients + Scheduled Payouts) -->
            <div class="col-lg-4">
                <!-- Served Clients -->
                <div class="card served-clients-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0">SERVED CLIENTS</h5>
                        <div class="mb-3">
                            <label for="filterCriteria" class="form-label">Filter By:</label>
                            <select id="filterCriteria" class="form-select">
                                <option value="vulnerability">Vulnerability</option>
                                <option value="age_group">Age Group</option>
                                <option value="cases">Case(s)</option>
                            </select>
                        </div>
                        <div class="served-clients-chart-wrapper">
                            <div class="chart-circle-outer">
                                <div class="chart-circle-inner">
                                    <div class="chart-center-value" id="chartCenterValue">{{ $servedClients }}</div>
                                </div>
                            </div>
                            <div class="legend-list" id="chartLegendList"></div>
                        </div>
                    </div>
                </div>

                <!-- Scheduled Payouts -->
                <div class="card table-card">
                    <div class="card-body">
                        <h5 class="card-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0">SCHEDULED PAYOUTS</h5>
                        <div class="table-responsive-wrapper">
                            <table class="upcoming-payouts-table">
                                <thead>
                                    <tr>
                                        <th>Schedule</th>
                                        <th>Municipality</th>
                                        <th>Payout Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($scheduledPayouts as $payout)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payout->payout_date)->format('M d, Y') }}</td>
                                            <td>{{ $payout->client->municipality->name ?? '-' }}</td>
                                            <td>{{ $payout->client->full_name ?? $payout->client->payee->full_name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No scheduled payouts.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (Upcoming Payouts + Previous Payouts) -->
            <div class="col-lg-8">
                <!-- Upcoming Payouts -->
                <div class="card table-card mb-4">
                    <div class="card-body">
                        <h5 class="card-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0">UPCOMING PAYOUTS</h5>
                        <div class="table-responsive-wrapper">
                            <table class="upcoming-payouts-table">
                                <thead>
                                    <tr>
                                        <th>Schedule</th>
                                        <th>Municipality</th>
                                        <th>Payout Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($upcomingPayouts as $payout)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payout->payout_date)->format('M d, Y') }}</td>
                                            <td>{{ $payout->client->municipality->name ?? '-' }}</td>
                                            <td>{{ $payout->client->full_name ?? $payout->client->payee->full_name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Previous Payouts -->
                <div class="card table-card">
                    <div class="card-body">
                        <h5 class="card-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0">PREVIOUS PAYOUTS</h5>
                        <div class="table-responsive-wrapper">
                            <table class="upcoming-payouts-table">
                                <thead>
                                    <tr>
                                        <th>Schedule</th>
                                        <th>Municipality</th>
                                        <th>Payout Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($previousPayouts as $payout)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payout->payout_date)->format('M d, Y') }}</td>
                                            <td>{{ $payout->client->municipality->name ?? '-' }}</td>
                                            <td>{{ $payout->client->full_name ?? $payout->client->payee->full_name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No previous payouts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const filterCriteria = document.getElementById("filterCriteria");
        const chartCenterValue = document.getElementById("chartCenterValue");
        const chartLegendList = document.getElementById("chartLegendList");

        //  Pass Laravel data to JS
        const data = {
            vulnerability: @json($vulnerabilityData),
            age_group: @json($ageGroupData),
            cases: @json($caseData)
        };

        // Function to update the chart
        function updateChart(category) {
            const categoryData = data[category];
            const total = Object.values(categoryData).reduce((sum, value) => sum + value, 0);

            // Update center value
            chartCenterValue.textContent = total;

            // Update legend
            chartLegendList.innerHTML = "";
            for (const [key, value] of Object.entries(categoryData)) {
                const legendItem = document.createElement("div");
                legendItem.classList.add("legend-item");
                legendItem.innerHTML = `
                    <span class="legend-color-box" style="background-color: ${getColorForKey(key)};"></span>
                    ${capitalizeFirstLetter(key)} (${value})
                `;
                chartLegendList.appendChild(legendItem);
            }
        }

        // Helper function to get color for a key
        function getColorForKey(key) {
            const colors = {
                senior_citizen: "#FFA500",
                pwd: "#FF4500",
                solo_parent: "#1E90FF",
                four_ps: "#32CD32",
                others: "#808080",
                "0-18": "#FFA500",
                "19-35": "#FF4500",
                "36-60": "#1E90FF",
                "60+": "#32CD32",
                ckd: "#FFA500",
                cancer: "#FF4500",
                heart_illness: "#1E90FF",
                diabetes: "#32CD32",
                hypertension: "#808080",
                others: "#808080"
            };
            return colors[key] || "#000000";
        }

        // Helper function to capitalize the first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, " ");
        }

        // Initial chart update
        updateChart("vulnerability");

        // Update chart on filter change
        filterCriteria.addEventListener("change", function () {
            updateChart(this.value);
        });
    });
</script>
@endsection