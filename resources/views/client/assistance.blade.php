@extends('layouts.app')

@section('content')

<main class="flex-1 p-4 overflow-auto">
    <h1 class="fs-3 fw-bold text-dark mb-4">Assistance Management</h1>

    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 gap-3">
        <button class="btn btn-success text-white" onclick="openAssistanceModalFromSearch()">
            <i class="fas fa-plus me-2"></i> Add Assistance
        </button>


        <a href="{{ route('claims.grouped') }}" class="btn btn-outline-primary mb-3">
            View Grouped Payouts
        </a>
        <form method="GET" action="{{ route('clients.assistance') }}" class="input-group w-100" style="max-width: 350px;">
            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Search client name..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

    </div>

    <div class="table-responsive bg-white p-3 rounded shadow-sm">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Client Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Contact Number</th>
                    <th>Representative</th>
                    <th>Representative Contact</th>
                    <th>Municipality</th>
                    <th>Medical Case</th>
                    <th>Assistance Type</th>
                    <th>Assistance Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    @php
                        $assistance = $client->assistances->first();
                        $claim = $assistance ? \App\Models\Claim::where('client_assistance_id', $assistance->id)->first() : null;
                        $status = $claim->status ?? 'pending';
                    @endphp
                    <tr>
                        <td>{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</td>
                        <td>{{ $client->sex }}</td>
                        <td>{{ $client->age }}</td>
                        <td>{{ $client->contact_number }}</td>
                        <td>
    {{ $assistance->payee && !empty($assistance->payee->full_name) ? $assistance->payee->full_name : '-' }}</td>
                        <td>{{ optional($assistance->payee)->contact_number ?? '-' }}</td>
                        <td>{{ $client->municipality->name ?? '-' }}</td>
                        <td>{{ $assistance->medical_case ?? '-' }}</td>
                        <td>{{ $assistance->assistanceType->type_name ?? '-' }}</td>
                        <td>{{ $assistance->assistanceCategory->category_name ?? '-' }}</td>

                        <td>
                            <span class="fw-semibold 
                                {{ $status === 'approved' ? 'text-success' : ($status === 'disapproved' ? 'text-danger' : 'text-warning') }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <!-- Dropdown for status actions -->
                                <div class="dropdown">
                                    <a href="#" role="button" id="dropdownMenu{{ $client->id }}" data-bs-toggle="dropdown" aria-expanded="false" class="text-dark">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $client->id }}">
                                        <li>
                                            <form action="{{ route('claims.update-status', $client->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="fas fa-check me-2"></i> Approve
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('claims.update-status', $client->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="disapproved">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-times me-2"></i> Disapprove
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('claims.update-status', $client->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="fas fa-clock me-2"></i> Pending
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                

                                

                                <!-- View Client -->
                                <a href="{{ route('clients.show', $client->id) }}" class="text-success" title="View Client">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-muted">No clients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<!-- Add Assistance Modal -->
<div id="addAssistanceModal" class="modal d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center modal-overlay" style="z-index: 1050;">
    <div class="modal-content bg-white rounded shadow-lg p-4 overflow-auto" style="max-width: 800px; max-height: 90vh; width: 100%;">
        <h2 class="text-center fw-bold w-100 mb-4 fs-1">Add Assistance</h2>
        <form id="assistanceForm" method="POST" action="{{ route('client-assistances.store') }}">
            @csrf

            <div class="mb-3 position-relative">
                <label for="search_client_input" class="form-label">Search Client:</label>
                <input type="text" id="search_client_input" class="form-control" placeholder="Type client name..." autocomplete="off">
                <input type="hidden" name="client_id" id="modal_client_id">
                <div id="client_search_results" class="list-group position-absolute w-100 z-3" style="max-height: 200px; overflow-y: auto;"></div>
            </div>

            <div class="mb-3">
                <label for="date_received_request" class="form-label">Date Received:</label>
                <input type="date" name="date_received_request" id="date_received_request" class="form-control" required>
            </div>

            <!-- Representative Toggle -->
            <div class="form-check mb-3">
                <input type="checkbox" name="has_representative" id="has_representative" class="form-check-input" value="1" onchange="toggleRepresentativeFields()">
                <label class="form-check-label" for="has_representative">Client has a Representative?</label>
            </div>

            <!-- Representative Details -->
            <div id="representativeFields" style="display: none;">
                <div class="form-group">
                    <label>Representative First Name:</label>
                    <input type="text" name="representative_first_name" class="form-control" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Representative Middle Name:</label>
                    <input type="text" name="representative_middle_name" class="form-control" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Representative Last Name:</label>
                    <input type="text" name="representative_last_name" class="form-control" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Relationship to the Client:</label>
                    <input type="text" name="relationship" class="form-control" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Representative Contact Number:</label>
                    <input type="text" name="representative_contact_number" class="form-control" autocomplete="off">
                </div>

                <!-- Proof of Relationship -->
                <div class="form-check mt-2">
                    <input type="checkbox" name="proof_of_relationship" id="proof_of_relationship" class="form-check-input" value="1">
                    <label for="proof_of_relationship" class="form-check-label">Proof of Relationship Provided?</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Assistance Type:</label>
                <select name="assistance_type_id" id="assistance_type" class="form-select" required>
                    <option value="">Select Assistance Type</option>
                    @foreach($assistanceTypes as $type)
                        <option value="{{ $type->id }}" data-name="{{ strtolower($type->type_name) }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>

            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Assistance Categories:</label>
                <div id="categories_section" class="ps-3">
                    <p class="text-muted">Please select an assistance type to view categories.</p>
                </div>
            </div>

            <!-- medical case -->
            <div class="mb-3" id="medical_case_section" style="display: none;">
                <label class="form-label">Medical Case:</label>
                <select name="medical_case" class="form-select">
                    <option value="">Select Case</option>
                    <option value="CKD">CKD</option>
                    <option value="Cancer">Cancer</option>
                    <option value="Heart Illness">Heart Illness</option>
                    <option value="Diabetes">Diabetes</option>
                    <option value="Hypertension">Hypertension</option>
                    <option value="Others">Others</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Requirements:</label>
                <div id="requirements_section" class="ps-3">
                    <p class="text-muted">Please select an assistance type to view requirements.</p>
                </div>
            </div>

            <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" onclick="closeAssistanceModal()">Cancel</button>
                <button type="submit" class="btn btn-success" id="saveAssistanceBtn" disabled>Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssistanceModalFromSearch() {
    document.getElementById('search_client_input').value = '';
    document.getElementById('modal_client_id').value = '';
    document.getElementById('client_search_results').innerHTML = '';
    document.getElementById('addAssistanceModal').classList.remove('d-none');
}

function closeAssistanceModal() {
    document.getElementById('addAssistanceModal').classList.add('d-none');
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

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search_client_input');
    const resultsContainer = document.getElementById('client_search_results');
    const clientIdField = document.getElementById('modal_client_id');
    const typeSelect = document.getElementById('assistance_type');
    const saveBtn = document.getElementById('saveAssistanceBtn');

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }

        fetch(`/api/search-clients?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';

                if (!data.length) {
                    resultsContainer.innerHTML = '<div class="list-group-item text-muted">No matches found.</div>';
                    return;
                }

                data.forEach(client => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${client.first_name} ${client.middle_name} ${client.last_name}`;
                    item.addEventListener('click', () => {
                        searchInput.value = item.textContent;
                        clientIdField.value = client.id;
                        resultsContainer.innerHTML = '';
                        validate(); 
                    });
                    resultsContainer.appendChild(item);
                });
            });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('date_received_request');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
    });

    document.addEventListener('click', function (e) {
        setTimeout(() => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.innerHTML = '';
            }
        }, 100);
    });

    // medical case
    document.getElementById('assistance_type')?.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const selectedName = selected.getAttribute('data-name');
        const medicalCaseSection = document.getElementById('medical_case_section');

        if (selectedName?.includes('medical')) {
            medicalCaseSection.style.display = 'block';
        } else {
            medicalCaseSection.style.display = 'none';
        }
    });

    typeSelect.addEventListener('change', function () {
        const typeId = this.value;
        saveBtn.disabled = true;

        fetch(`/get-requirements/${typeId}`)
            .then(res => res.json())
            .then(data => {
                let html = data.length ? '' : '<p>No requirements found.</p>';
                data.forEach(r => {
                    html += `<div class="form-check">
                                <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input requirement-checkbox">
                                <label class="form-check-label">${r.requirement_name}</label>
                            </div>`;
                });
                document.getElementById('requirements_section').innerHTML = html;
                attachValidation();
            });

        fetch(`/get-categories/${typeId}`)
        .then(res => res.json())
        .then(data => {
            let html = data.length ? '' : '<p>No categories found.</p>';
            data.forEach(c => {
                html += `<div class="form-check">
                            <input type="radio" name="assistance_category_id" value="${c.id}" class="form-check-input category-radio" id="category-${c.id}">
                            <label class="form-check-label" for="category-${c.id}">${c.category_name}</label>
                        </div>`;
            });
            document.getElementById('categories_section').innerHTML = html;
            attachValidation();
        });

    });

    function attachValidation() {
        const requirements = document.querySelectorAll('.requirement-checkbox');
        const categories = document.querySelectorAll('.category-radio');

        requirements.forEach(cb => cb.addEventListener('change', validate));
        categories.forEach(rb => rb.addEventListener('change', validate));
        validate();
    }

    function validate() {
        const clientSelected = clientIdField.value.trim() !== '';
        const allChecked = [...document.querySelectorAll('.requirement-checkbox')].every(cb => cb.checked);
        const categorySelected = document.querySelector('.category-radio:checked') !== null;
        const noRequirements = document.querySelectorAll('.requirement-checkbox').length === 0;

        console.log({
        clientSelected,
        clientId: clientIdField.value,
        categorySelected,
        allChecked,
        noRequirements,
        finalStatus: clientSelected && categorySelected && (noRequirements || allChecked)
    });

        saveBtn.disabled = !(clientSelected && categorySelected && (noRequirements || allChecked));
    }
});
</script>

@endsection