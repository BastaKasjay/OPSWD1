@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="container">
    <h1>Clients</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('clients.create') }}" class="btn btn-primary">Add Client</a>

        <!-- Search form -->
        <form method="GET" action="{{ route('clients.index') }}" class="d-flex">
            @if (request('municipality_id'))
                <input type="hidden" name="municipality_id" value="{{ request('municipality_id') }}">
            @endif

            <input type="text" name="search" class="form-control mr-2" value="{{ request('search') }}" placeholder="Search by name" oninput="if (this.value === '') this.form.submit()">
            <button type="submit" class="btn btn-primary ml-2">Search</button>
        </form>
    </div>
    


    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Sex</th>
                <th>Age</th>
                <th>Address</th>
                <th>Contact Number</th>

                <!-- Municipality filter inside table header -->
                <th>
                    Municipality
                    <form method="GET" action="{{ route('clients.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="municipality_id" class="form-control" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->id }}" {{ request('municipality_id') == $municipality->id ? 'selected' : '' }}>
                                    {{ $municipality->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </th>

                <th>Assistance Type</th>
                <th>Assistance Category</th>
                <th>
                    Vulnerability Sectors 
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Summary
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><strong>All:</strong> {{ $totalVulnerable }}</li>
                            @foreach ($vulnerabilityCounts as $sector)
                                <li class="dropdown-item">
                                    {{ $sector->name }}: {{ $sector->clients_count }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </th>

                <th>Representative First Name</th>
                <th>Representative Middle Name</th>
                <th>Representative Last Name</th>
                <th>Representative Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td style="white-space: nowrap;">
                        <a href="{{ route('clients.show', $client->id) }}">
                            {{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}
                        </a>
                    </td>

                    <td>{{ $client->sex }}</td>
                    <td>{{ $client->age }}</td>
                    <td>{{ $client->address }}</td>
                    <td>{{ $client->contact_number }}</td>
                    <td>{{ $client->municipality ? $client->municipality->name : '-' }}</td>
                    <td>{{ $client->assistanceType ? $client->assistanceType->type_name : '-' }}</td>
                    <td>{{ $client->assistanceCategory ? $client->assistanceCategory->category_name : '-' }}</td>
                    <td>
                        @if ($client->vulnerabilitySectors->isNotEmpty())
                            <ul>
                                @foreach ($client->vulnerabilitySectors as $sector)
                                    <li>{{ $sector->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            None
                        @endif
                    </td>
                    <td>{{ $client->representative_first_name }}</td>
                    <td>{{ $client->representative_middle_name }}</td>
                    <td>{{ $client->representative_last_name }}</td>
                    <td>{{ $client->representative_contact_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection