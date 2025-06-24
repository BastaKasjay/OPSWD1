@extends('layouts.app')

@section('content')
<div class="container">
    

    <div class="card p-4">
        <h4>{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</h4>

        <p><strong>Sex:</strong> {{ $client->sex }}</p>
        <p><strong>Age:</strong> {{ $client->age }}</p>
        <p><strong>Address:</strong> {{ $client->address }}</p>
        <p><strong>Contact Number:</strong> {{ $client->contact_number }}</p>

        <h5>Municipality</h5>
        <p>{{ optional($client->municipality)->name ?? '-' }}</p>

        <h5>Assistance Type</h5>
        <p>{{ optional($client->assistanceType)->type_name ?? '-' }}</p>

        <h5>Assistance Category</h5>
        <p>{{ optional($client->assistanceCategory)->category_name ?? '-' }}</p>

        <h5>Vulnerability Sectors</h5>
        @if ($client->vulnerabilitySectors->isNotEmpty())
            <ul>
                @foreach ($client->vulnerabilitySectors as $sector)
                    <li>{{ $sector->name }}</li>
                @endforeach
            </ul>
        @else
            <p>None</p>
        @endif

        <h5>Representative</h5>
        <p><strong>First Name:</strong> {{ $client->representative_first_name ?? '-' }}</p>
        <p><strong>Middle Name:</strong> {{ $client->representative_middle_name ?? '-' }}</p>
        <p><strong>Last Name:</strong> {{ $client->representative_last_name ?? '-' }}</p>
        <p><strong>Contact:</strong> {{ $client->representative_contact_number ?? '-' }}</p>

        <div class="mt-3">
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">Edit Client</a>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
