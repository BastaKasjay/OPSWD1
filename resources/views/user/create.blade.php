@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">User Management</h1>

    {{-- ✅ Bootstrap Flash Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Create User Button --}}
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fas fa-user-plus me-2"></i> Add User
    </button>

    {{-- Users Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Municipality</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->municipality->name ?? '-' }}</td>
                        <td>
                            {{-- Edit / Delete Buttons here --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Create User Modal --}}
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role:</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="officer">Officer</option>
                                <option value="encoder">Encoder</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Municipality:</label>
                            <select name="municipality_id" class="form-select" required autocomplete="off">
                                <option value="">Select Municipality</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm Password:</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Create</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
