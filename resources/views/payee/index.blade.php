@extends('layouts.app')

@section('title', 'Payees')

@section('content')
<h1>Payees</h1>

<a href="{{ route('payees.create') }}">Create Payee</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Relationship</th>
    </tr>

    @foreach ($payees as $payee)
        <tr>
            <td>{{ $payee->id }}</td>
            <td>{{ $payee->full_name }}</td>
            <td>{{ $payee->relationship }}</td>
        </tr>
    @endforeach
</table>
@endsection
