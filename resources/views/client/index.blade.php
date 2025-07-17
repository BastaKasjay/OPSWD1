@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="clients-container bg-white p-6 rounded-lg shadow-md overflow-x-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 table table-bordered table-striped">Clients</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary bg-mint-green-700 hover:bg-mint-green-900 text-white font-semibold px-4 py-2 rounded-lg shadow" onclick="openCreateModal()">Add Client</button>

        <!-- Search form -->
        <form method="GET" action="{{ route('clients.index') }}" class="d-flex">
            @if (request('municipality_id'))
                <input type="hidden" name="municipality_id" value="{{ request('municipality_id') }}">
            @endif

            <input type="text" name="search" class="form-control mr-2" value="{{ request('search') }}" placeholder="Search by name" oninput="if (this.value === '') this.form.submit()">
            <button type="submit" class="btn btn-primary ml-2">Search</button>
        </form>
    </div>

    <div class="rounded-lg">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Contact Number</th>
                    <th>Valid_ID</th>
                    <th>
                        Municipality
                        <form method="GET" action="{{ route('clients.index') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="municipality_id" class="form-control" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality->id }}" {{ request('municipality_id') == $municipality->id ? 'selected' : '' }}>
                                        {{ $municipality->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </th>
                    <th>Vulnerability Sectors</th>
                    
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td style="white-space: nowrap;">
                            {{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}
                        </td>
                        <td>{{ $client->sex }}</td>
                        <td>{{ $client->age }}</td>
                        <td>{{ $client->address }}</td>
                        <td>{{ $client->contact_number }}</td>
                        <td>{{ $client->valid}}</td>
                        <td>{{ $client->municipality ? $client->municipality->name : '-' }}</td>
                        <td>
                            @if ($client->vulnerabilitySectors->isNotEmpty())
                                <ul>
                                    @foreach ($client->vulnerabilitySectors as $sector)
                                        <li>{{ $sector->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                None
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                
                                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-success" title="View Client">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>


                
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Creating Client -->
<div id="createModal" class="modal-create d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center modal-overlay" style="z-index: 1050;">
    <div class="modal-content bg-white rounded shadow-lg p-4 overflow-auto" style="max-width: 800px; max-height: 90vh; width: 100%;">
        <h2 class="text-center fw-bold w-100 mb-4 fs-1">Add Client</h2>
        <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <!-- Basic client info -->
                <div class="col-md-6">
                    <label class="form-label" >First Name:</label>
                    <input type="text" name="first_name" class="form-control" required autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Middle Name:</label>
                    <input type="text" name="middle_name" class="form-control" autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name:</label>
                    <input type="text" name="last_name" class="form-control" required autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sex:</label>
                    <select name="sex" class="form-select" required>
                        <option value="">Select Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Age:</label>
                    <input type="number" name="age" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Municipality:</label>
                    <select name="municipality_id" class="form-select" required>
                        <option value="">Select Municipality</option>
                        @foreach($municipalities as $municipality)
                            <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-6">
                    <label class="form-label">Valid ID:</label>
                    <select name="valid_id" class="form-select" required>
                        <option value="">Select Valid ID</option>
                        <option value="PhilHealth ID">PhilHealth ID</option>
                        <option value="SSS ID">SSS ID</option>
                        <option value="GSIS ID">GSIS ID</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver’s License">Driver’s License</option>
                        <option value="Voter’s ID">Voter’s ID</option>
                        <option value="Postal ID">Postal ID</option>
                        <option value="PRC ID">PRC ID</option>
                        <option value="UMID">UMID</option>
                        <option value="Barangay ID">Barangay ID</option>
                        <option value="Senior Citizen ID">Senior Citizen ID</option>
                        <option value="PWD ID">PWD ID</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">ID No.</label>
                    <input type="number" name="id_no" class="form-control" required>
                </div>

                

                <div class="col-md-12">
                    <label class="form-label">Address:</label>
                    <input type="text" name="address" class="form-control" required autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Number:</label>
                    <input type="text" name="contact_number" class="form-control" autocomplete="off">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Birthday:</label>
                    <input type="date" name="birthday" class="form-control"
                        min="1900-01-01"
                        max="{{ now()->format('Y-m-d') }}">
                </div>

                

                <!-- Vulnerability Sectors -->
                <div class="col-md-12">
                    <label class="form-label">Vulnerability Sectors:</label>
                    <div class="row">
                        @foreach($vulnerabilitySectors as $sector)
                            <div class="form-check col-md-4">
                                <input type="checkbox" name="vulnerability_sectors[]" value="{{ $sector->id }}" class="form-check-input" id="sector_{{ $sector->id }}">
                                <label class="form-check-label" for="sector_{{ $sector->id }}">{{ $sector->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Buttons -->
                <div class="col-md-12 text-end mt-3">
                    <button type="button" class="btn btn-secondary me-2" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
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



    // Edit Modal Scripts
    function openEditModal(id) {
        const modal = document.getElementById('editModal_' + id);
        modal.classList.remove('d-none');

        const repCheckbox = document.getElementById('has_representative_' + id);
        const repFields = document.getElementById('representativeFields_' + id);

        if (repCheckbox && repFields) {
            // Show/hide on load
            repFields.style.display = repCheckbox.checked ? 'block' : 'none';

            // Toggle on change
            repCheckbox.addEventListener('change', () => {
                repFields.style.display = repCheckbox.checked ? 'block' : 'none';
            });
        }

        // (Optional) Category loading logic if you're using dynamic dropdowns
        const typeSelect = modal.querySelector('#assistance_type');
        const categorySelect = modal.querySelector('#assistance_category');
        const selectedCategoryId = categorySelect?.getAttribute('data-selected');

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
