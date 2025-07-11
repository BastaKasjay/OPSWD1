@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <h1>Dashboard</h1>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle rounded-pill" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-globe"></i> ALL
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="#">Option 1</a></li>
                    <li><a class="dropdown-item" href="#">Option 2</a></li>
                    <li><a class="dropdown-item" href="#">Option 3</a></li>
                </ul>
            </div>
        </header>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="dashboard-card card-purple">
                    <div class="card-title">Payouts</div>
                    <div class="card-value">6</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-orange">
                    <div class="card-title">Total Clients</div>
                    <div class="card-value">1,207</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-green">
                    <div class="card-title">Disbursed Amount</div>
                    <div class="card-value">₱84,000</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card card-red">
                    <div class="card-title">Total Accomplishment Rate</div>
                    <div class="card-value">3.64 %</div>
                </div>
            </div>
        </div>

        <!-- Served Clients and Upcoming Payouts -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card served-clients-card">
                    <div class="card-body">
                        <h5 class="card-title">SERVED CLIENTS</h5>
                        <div class="served-clients-chart-wrapper">
                            <div class="chart-circle-outer">
                                <div class="chart-circle-inner">
                                    <div class="chart-center-value">43</div>
                                </div>
                            </div>
                            <div class="legend-list">
                                <div class="legend-item">
                                    <span class="legend-color-box legend-orange"></span> Medical Assistance
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color-box legend-red"></span> Funeral
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color-box legend-blue"></span> Educational Assistance
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color-box legend-green"></span> Transportation
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card table-card">
                    <div class="card-body">
                        <h5 class="card-title">UPCOMING PAYOUTS</h5>
                        <div class="table-responsive-wrapper">
                            <table class="upcoming-payouts-table">
                                <thead>
                                    <tr>
                                        <th>Schedule</th>
                                        <th>Payout Code</th>
                                        <th>Payout Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Dec. 24, 2024 - Dec. 25, 2024</td>
                                        <td>AKAP-12325324</td>
                                        <td>CONGOS AMBELARGOI, PAYOUT</td>
                                    </tr>
                                    <tr>
                                        <td>Dec. 24, 2024 - Dec. 26, 2024</td>
                                        <td>SAMP</td>
                                        <td>STEPH SAMPLE</td>
                                    </tr>
                                    <tr>
                                        <td>Dec. 16, 2024 - Dec. 18, 2024</td>
                                        <td>MIKETTETINGPAYOUT</td>
                                        <td>MIKE TESTING PAYOUT</td>
                                    </tr>
                                    <tr>
                                        <td>Dec. 16, 2024 - Dec. 19, 2024</td>
                                        <td>AKAP-CL (ITOGON-FA)</td>
                                        <td>ITOGON PAY-OUT</td>
                                    </tr>
                                    <tr>
                                        <td>Dec. 24, 2024 - Dec. 26, 2024</td>
                                        <td>KAPANGAN</td>
                                        <td>KAPANGAN PAYOUT</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection