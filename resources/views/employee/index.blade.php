@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="flex-1 p-4 overflow-auto">
    <h1 class="text-3xl fw-semibold text-dark mb-4">Employees</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Add Employee Button -->
        <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            <i class="fas fa-user-plus me-1"></i> Add Employee
        </button>

        <!-- <form method="GET" action="{{ route('employees.index') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search employees">
            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Search</button>
        </form> -->
    </div>

    @if(request('search'))
        <div class="d-flex justify-content-end text-secondary mb-2">
            Showing {{ $employees->count() }} result(s) for "{{ request('search') }}"
        </div>
    @endif

<div class="table-responsive bg-white p-3 rounded shadow-sm mb-4">
    <style>
        .employee-table, .employee-table th, .employee-table td {
            border: none !important;
        }
    </style>
        <table class="table table-hover align-middle mb-0 employee-table" style="width:100%; background: transparent; border: none; font-family: 'Inter', sans-serif; border-collapse: separate; border-spacing: 0;">
            <thead class="bg-white" style="border: none;">
                <tr style="border: none;">
                    <th class="bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">ID</th>
                    <th class="bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">First Name</th>
                    <th class="bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Middle Name</th>
                    <th class="bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Last Name</th>
                    <th class="bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Office</th>
                    <th class="text-center bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody style="border: none;">
                    @forelse($employees as $employee)
                        <tr style="border: none;">
                            <td style="border: none;">{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td style="border: none;">{{ $employee->first_name }}</td>
                            <td style="border: none;">{{ $employee->middle_name ?? '-' }}</td>
                            <td style="border: none;">{{ $employee->last_name }}</td>
                            <td style="border: none;">{{ $employee->office }}</td>
                            <td class="text-center" style="border: none;">
                                <div class="d-flex justify-content-center gap-1 assistance-actions" style="border: none;">
                                    <button type="button" class="btn p-1" style="border: none; background: none; color: #76AE91;" data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $employee->id }}" title="Edit Employee">
                                        <i class="fas fa-edit"></i>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn p-1 text-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $employee->id }}" title="Delete Employee">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Employee Modal -->
                        <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1" aria-labelledby="editEmployeeModalLabel{{ $employee->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                                        <div class="modal-header border-0 pb-0">
                                            <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" id="editEmployeeModalLabel{{ $employee->id }}">Edit Employee</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-4 pt-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">First Name</label>
                                                <input type="text" name="first_name" value="{{ $employee->first_name }}" class="form-control rounded border border-success bg-light" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Middle Name</label>
                                                <input type="text" name="middle_name" value="{{ $employee->middle_name }}" class="form-control rounded border border-success bg-light">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Last Name</label>
                                                <input type="text" name="last_name" value="{{ $employee->last_name }}" class="form-control rounded border border-success bg-light" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Office</label>
                                                <input type="text" name="office" value="{{ $employee->office }}" class="form-control rounded border border-success bg-light" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2">
                                            <button class="btn btn-success rounded-pill px-4" type="submit">Update</button>
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="confirmDeleteModal{{ $employee->id }}" tabindex="-1" aria-labelledby="confirmDeleteLabel{{ $employee->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger bg-opacity-10">
                                    <h5 class="modal-title text-danger" id="confirmDeleteLabel{{ $employee->id }}">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    Are you sure you want to delete <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>?
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger px-4">Yes, Delete</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr style="border: none;">
                            <td colspan="6" class="text-center text-muted" style="border: none;">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Employee Modal -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('employees.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                <div class="modal-header border-0 pb-0" style="background: none;">
                    <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" style="font-size: 1.5rem;">Add New Employee</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-4 pt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control rounded border border-success bg-light" style="box-shadow: none;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Office</label>
                        <input type="text" name="office" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2" style="background: none;">
                    <button class="btn btn-success custom-green-btn rounded-pill px-4" type="submit">Save</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
