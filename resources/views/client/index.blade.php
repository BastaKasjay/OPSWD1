@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="flex-1 p-4 overflow-auto">
    <h1 class="text-3xl fw-semibold text-dark mb-4">Clients</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-success custom-green-btn rounded px-4 py-2 fw-semibold shadow-sm" style="border-radius: 0.7rem; font-size: 1rem; background-color: #198754; border: none;" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Add Client
        </button>

        <!-- Search form -->
        <form method="GET" action="{{ route('clients.index') }}" class="d-flex">
            @if (request('municipality_id'))
                <input type="hidden" name="municipality_id" value="{{ request('municipality_id') }}">
            @endif

            <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Search by name" autocomplete="off" oninput="if (this.value === '') this.form.submit()">
            <button type="submit" class="btn btn-success custom-green-btn rounded px-4 py-2 fw-semibold shadow-sm" style="border-radius: 0.7rem; font-size: 1rem; background-color: #198754; border: none;" >Search</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width:100%; font-family: 'Inter', sans-serif;">
                <thead class="bg-white" style="border: none;">
                    <tr style="border: none;">
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">ID</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Full Name</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Sex</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Age</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Address</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Contact Number</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Valid ID</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Municipality</th>
                        <th class="px-4 py-2 assistance-th bg-success bg-opacity-10 text-nowrap" style="color: #374151; font-size: 1rem; font-weight: 600;">Vulnerability Sectors</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr style="border: none; font-family: 'Inter', sans-serif; font-size: 0.95rem; color: #374151;">
                            <td class="px-4 py-2 assistance-td">{{ $client->id }}</td>
                            <td class="text-nowrap px-4 py-2 assistance-td">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->sex }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->age }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->address }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->contact_number }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->valid_id }}</td>
                            <td class="px-4 py-2 assistance-td">{{ $client->municipality ? $client->municipality->name : '-' }}</td>
                            <td class="px-4 py-2 assistance-td">
                                @if ($client->vulnerabilitySectors->isNotEmpty())
                                    <ul class="mb-0 ps-0 list-unstyled">
                                        @foreach ($client->vulnerabilitySectors as $sector)
                                            <li>{{ $sector->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    None
                                @endif
                            </td>
                        
                        </tr>

                    <!-- edit modal -->
                    <div id="editModal_{{ $client->id }}" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 1050; background: rgba(0,0,0,0.5);">
                        <div class="bg-white" style="max-width: 800px; width: 100%; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem; max-height: 90vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="h5 fw-bold text-success mb-0 bg-success bg-opacity-10 rounded py-2 px-3 w-100 text-center">Edit Info</h2>
                                <button type="button" class="btn-close ms-2" onclick="closeEditModal({{ $client->id }})"></button>
                            </div>
                            <form action="{{ route('clients.update', $client->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <!-- Personal Details -->
                                    <div class="col-md-6">
                                        <label class="form-label">First Name:</label>
                                        <input type="text" name="first_name" class="form-control rounded border border-success" value="{{ $client->first_name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Middle Name:</label>
                                        <input type="text" name="middle_name" class="form-control rounded border border-success" value="{{ $client->middle_name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name:</label>
                                        <input type="text" name="last_name" class="form-control rounded border border-success" value="{{ $client->last_name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sex:</label>
                                        <select name="sex" class="form-select rounded border border-success" required>
                                            <option value="Male" {{ $client->sex == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $client->sex == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Age:</label>
                                        <input type="number" name="age" class="form-control rounded border border-success" value="{{ $client->age }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address:</label>
                                        <input type="text" name="address" class="form-control rounded border border-success" value="{{ $client->address }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number:</label>
                                        <input type="text" name="contact_number" class="form-control rounded border border-success" value="{{ $client->contact_number }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Birthday:</label>
                                        <input type="date" name="birthday" class="form-control rounded border border-success" value="{{ $client->birthday }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Municipality:</label>
                                        <select name="municipality_id" class="form-select rounded border border-success" required>
                                            <option value="">Select Municipality</option>
                                            @foreach($municipalities as $municipality)
                                                <option value="{{ $municipality->id }}" {{ $client->municipality_id == $municipality->id ? 'selected' : '' }}>
                                                    {{ $municipality->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Vulnerability Sectors:</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($vulnerabilitySectors as $sector)
                                                <div class="form-check">
                                                    <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" 
                                                        class="form-check-input" id="sector_{{ $sector->id }}"
                                                        {{ $client->vulnerabilitySectors->contains($sector->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    
                                </div>
                                <div class="mt-4 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-success custom-green-btn rounded-pill px-4">Save Changes</button>
                                    <a href="{{ route('clients.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                
                @endforeach
            </tbody>
        </table>
        @if ($clients->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="mb-0 text-muted">
                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} results
                </p>
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($clients->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $clients->previousPageUrl() }}" rel="prev">Prev</a>
                            </li>
                        @endif

                        {{-- Page Number Links --}}
                        @for ($i = 1; $i <= $clients->lastPage(); $i++)
                            <li class="page-item {{ $clients->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $clients->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($clients->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $clients->nextPageUrl() }}" rel="next">Next</a>
                            </li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif

    </div>
</div>


<!-- Modal for Adding Client -->
<div id="createModal" class="modal d-none slide-in-modal position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center modal-overlay" style="z-index: 1050;">
    <div class="modal-dialog modal-lg" style="max-width: 800px;">
        <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                <div class="modal-header border-0 pb-0" style="background: none;">
                    <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" style="font-size: 1.5rem;">Add Client</h2>
                    <button type="button" class="btn-close" onclick="closeCreateModal()"></button>
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
                        <label class="form-label fw-semibold">Sex</label>
                        <select name="sex" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Age</label>
                        <input type="number" name="age" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Municipality</label>
                        <select name="municipality_id" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                            <option value="">Select Municipality</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control rounded border border-success bg-light" style="box-shadow: none;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Birthday</label>
                        <input type="date" name="birthday" class="form-control rounded border border-success bg-light" min="1900-01-01" max="{{ now()->format('Y-m-d') }}" style="box-shadow: none;">
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="valid_id" id="valid_id" value="1" {{ old('valid_id', $client->valid_id ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="valid_id">Valid ID Presented?</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Vulnerability Sectors</label>
                        <div class="row">
                            @foreach($vulnerabilitySectors as $sector)
                                <div class="form-check col-md-4">
                                    <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" class="form-check-input" id="sector_{{ $sector->id }}">
                                    <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2" style="background: none;">
                    <button class="btn btn-success custom-green-btn rounded-pill px-4" type="submit">Save</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" onclick="closeCreateModal()">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('d-none');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('d-none');
    }

    function toggleRepresentativeFields() {
        const checkbox = document.getElementById('has_representative');
        const repFields = document.getElementById('representativeFields');
        repFields.style.display = checkbox.checked ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleRepresentativeFields();
        document.getElementById('has_representative')?.addEventListener('change', toggleRepresentativeFields);
    });

    // Edit Modal Scripts
    function openEditModal(id) {
        const modal = document.getElementById('editModal_' + id);
        modal.classList.remove('d-none');

        const typeSelect = modal.querySelector('#assistance_type');
        const categorySelect = modal.querySelector('#assistance_category');
        const selectedCategoryId = categorySelect.getAttribute('data-selected');

        const repCheckbox = modal.querySelector('#has_representative');
        const repFields = modal.querySelector('#representativeFields');
        if (repCheckbox && repFields) {
            repFields.style.display = repCheckbox.checked ? 'block' : 'none';

            repCheckbox.addEventListener('change', () => {
                repFields.style.display = repCheckbox.checked ? 'block' : 'none';
            });
        }

        if (typeSelect && categorySelect) {
            const typeId = typeSelect.value;
            categorySelect.innerHTML = '<option>Loading...</option>';

            if (typeId) {
                fetch('/get-categories/' + typeId)
                    .then(response => response.json())
                    .then(data => {
                        categorySelect.innerHTML = '<option value="">Select Assistance Category</option>';
                        data.forEach(category => {
                            const selected = category.id == selectedCategoryId ? 'selected' : '';
                            categorySelect.innerHTML += `<option value="${category.id}" ${selected}>${category.category_name}</option>`;
                        });
                    });
            }
        }
    }
    

    function closeEditModal(id) {
        const modal = document.getElementById('editModal_' + id);
        modal.classList.add('d-none');
    }
</script>
@endsection
