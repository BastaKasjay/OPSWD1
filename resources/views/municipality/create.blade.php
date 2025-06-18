@extends('layouts.app')

@section('title', 'Create Municipality')

@section('content')
<h1>Create Municipality</h1>

<form action="{{ route('municipalities.store') }}" method="POST">
    @csrf
    <label>Name:</label><br>
    <input type="text" name="name"><br>

    <button type="submit">Save</button>
</form>
@endsection
