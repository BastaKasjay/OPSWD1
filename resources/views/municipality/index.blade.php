@extends('layouts.app')

@section('title', 'Municipalities')

@section('content')
<h1>Municipalities</h1>

<a href="{{ route('municipalities.create') }}">Add Municipality</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>

    @foreach ($municipalities as $municipality)
        <tr>
            <td>{{ $municipality->id }}</td>
            <td>{{ $municipality->name }}</td>
            <td>
                <a href="{{ route('municipalities.edit', $municipality->id) }}">Edit</a>
            </td>
        </tr>
    @endforeach
</table>
@endsection
