@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<h1>Clients</h1>

<a href="{{ route('clients.create') }}">Add Client</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Assistance Type</th>
        <th>Assistance Category</th>
        <th>Case</th>
        <th>Vulnerability</th>
        <th>Representative</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Contact No.</th>
        <th>Municipality</th>
        <th>Address</th>
        <th>Valid ID</th>
        <th>ID No.</th>
        <th>Assessed by</th>
    </tr>

    @foreach ($clients as $client)
        <tr>
            <td>{{ $client->id }}</td>
            <td>{{ $client->first_name }}</td>
            <td>{{ $client->last_name }}</td>
            <td>{{ $client->sex }}</td>
            <td>{{ $client->age }}</td>
            <td>{{ $client->assistance_type }}</td>
            <td>{{ $client->assistance_category }}</td>
            <td>{{ $client->case }}</td>
            <td>{{ $client->vulnerability }}</td>
            <td>{{ $client->representative }}</td>
            <td>{{ $client->contact_number }}</td>
            <td>{{ $client->municipality }}</td>
            <td>{{ $client->address }}</td>
            <td>{{ $client->valid_id }}</td>
            <td>{{ $client->id_number }}</td>
            <td>{{ $client->assessed_by }}</td>
        </tr>
    @endforeach
</table>
@endsection
