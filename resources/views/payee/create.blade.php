@extends('layouts.app')

@section('title', 'Create Payee')

@section('content')
<h1>Create Payee</h1>

<form action="{{ route('payees.store') }}" method="POST">
    @csrf

    <label>First Name:</label><br>
    <input type="text" name="first_name"><br>

    <label>Middle Name:</label><br>
    <input type="text" name="middle_name"><br>

    <label>Last Name:</label><br>
    <input type="text" name="last_name"><br>

    <label>Relationship:</label><br>
    <input type="text" name="relationship"><br>

    <button type="submit">Save</button>
</form>
@endsection
