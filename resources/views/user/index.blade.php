@extends('layouts.app')

@section('title', 'Users')


@section('content')
<div class="flex-1 p-4 overflow-auto">
    <h1 class="text-3xl fw-semibold text-dark mb-4">Users</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Add User Button -->
        <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-1"></i> Add User
        </button>

        <!-- <form method="GET" action="{{ route('users.index') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search users" oninput="if (this.value === '') this.form.submit()">
            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">Search</button>
        </form> -->
    </div>

    @if(request('search'))
        <div class="d-flex justify-content-end text-secondary mb-2">
            Showing {{ $users->total() }} result(s) for "{{ request('search') }}"
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width:100%; font-family: 'Inter', sans-serif;">
                <thead class="bg-white" style="border: none;">
                    <tr style="border: none;">
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">ID</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Username</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Name</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Role</th>
                        <th class="text-center px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="width: 70px; color: #374151; font-size: 1rem; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border: none; font-family: 'Inter', sans-serif; font-size: 0.95rem; color: #374151;">
                            <td class="px-4 py-2 assistance-td">{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $user->username }}</td>
                            <td class="px-4 py-2 assistance-td">
                                {{ $user->employee->first_name }}
                                @if($user->employee->middle_name)
                                    {{ strtoupper(substr($user->employee->middle_name, 0, 1)) }}.
                                @endif
                                {{ $user->employee->last_name }}
                            </td>
                            
                            <td class="px-4 py-2 assistance-td">{{ $user->roles->pluck('rolename')->join(', ') }}</td>

                            <td class="text-center px-4 py-2 assistance-td">
                                <div class="d-flex justify-content-center gap-1">
                                    <button 
                                        type="button"
                                        class="btn p-1"
                                        style="border: none; background: none;"
                                        title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-id="{{ $user->id }}"
                                        data-username="{{ $user->username }}"
                                        data-role-id="{{ $user->roles->first()->id ?? '' }}">
                                        <i class="fas fa-pen text-success"></i>
                                    </button>

                                    <button onclick="openDeleteModal('{{ $user->id }}')" class="btn p-1" style="border: none; background: none;" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editUserForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-sm rounded p-4">
                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0">
                        Edit User
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-4 pt-3">
                    <!-- Username -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control rounded border border-success bg-light" required>
                        @error('username')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password <small>(leave blank to keep current)</small></label>
                        <input type="password" name="password" id="edit_password" class="form-control rounded border border-success bg-light">
                    </div>


                    <!-- Role -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role_id" id="edit_role" class="form-control rounded border border-success bg-light" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->rolename) }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2">
                    <button class="btn btn-success rounded-pill px-4" type="submit">Save Changes</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>





    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-danger rounded-3">
            <div class="modal-header text-danger">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this User?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>



<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                <div class="modal-header border-0 pb-0" style="background: none;">
                    <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" style="font-size: 1.5rem;">Add New User</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-4 pt-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Select Employee</label>
                        <select name="employee_id" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role_id" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst($role->rolename) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
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

<script>
    function openDeleteModal(userId) {
        const form = document.getElementById('deleteForm');
        form.action = `/users/${userId}`; // Adjust this to your delete route
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const editUserModal = document.getElementById('editUserModal');

        editUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const username = button.getAttribute('data-username');
            const roleId = button.getAttribute('data-role-id');

            const form = document.getElementById('editUserForm');
            form.action = `/users/${id}`; // adjust if route prefix differs

            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = roleId;
            document.getElementById('edit_password').value = ''; // clear password
        });
    });


</script>

