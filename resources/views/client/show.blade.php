@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <button onclick="openEditModal({{ $client->id }})" class="btn btn-success custom-green-btn rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm" title="Edit Client">
            <i class="fas fa-edit"></i>
            <span class="d-none d-md-inline">Edit</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm p-4 mb-4">
        <h4 class="fw-bold text-success text-center mb-4 bg-success bg-opacity-10 rounded py-2">Personal Details</h4>
        <div class="row g-3 mb-2">
            <div class="col-md-4"><span class="fw-semibold">First Name:</span> {{ $client->first_name }}</div>
            <div class="col-md-4"><span class="fw-semibold">Middle Name:</span> {{ $client->middle_name }}</div>
            <div class="col-md-4"><span class="fw-semibold">Last Name:</span> {{ $client->last_name }}</div>
            <div class="col-md-4"><span class="fw-semibold">Municipality:</span> {{ optional($client->municipality)->name ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Sex:</span> {{ $client->sex }}</div>
            <div class="col-md-4"><span class="fw-semibold">Age:</span> {{ $client->age }}</div>
            <div class="col-md-4"><span class="fw-semibold">Birth Date:</span> {{ $client->birthday ? \Carbon\Carbon::parse($client->birthday)->format('F d, Y') : '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Contact:</span> {{ $client->contact_number ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Vulnerability Sectors:</span> {{ $client->vulnerabilitySectors->isNotEmpty() ? $client->vulnerabilitySectors->pluck('name')->join(', ') : 'None' }}</div>
        </div>
        <h5 class="fw-bold text-success text-center mb-3 bg-success bg-opacity-10 rounded py-2">Representative</h5>
        <div class="row g-3 mb-2">
            <div class="col-md-4"><span class="fw-semibold">First Name:</span> {{ $client->payee->first_name ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Middle Name:</span> {{ $client->payee->middle_name ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Last Name:</span> {{ $client->payee->last_name ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Contact:</span> {{ $client->payee->contact_number ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Relationship:</span> {{ $client->payee->relationship ?? '-' }}</div>
        </div>
        <h4 class="fw-bold text-success text-center mb-3 bg-success bg-opacity-10 rounded py-2">Assistance</h4>
        @if ($client->assistances && $client->assistances->isNotEmpty())
            @foreach ($client->assistances as $assistance)
                <div class="row g-3 mb-2">
                    <div class="col-md-4"><span class="fw-semibold">Assistance Type:</span> {{ $assistance->assistanceType->type_name ?? '-' }}</div>
                    <div class="col-md-4"><span class="fw-semibold">Assistance Category:</span> {{ $assistance->assistanceCategory->category_name ?? '-' }}</div>
                    <div class="col-md-4"><span class="fw-semibold">Medical Case:</span> {{ $assistance->medical_case ?? '-' }}</div>
                    <div class="col-md-4"><span class="fw-semibold">Date Received:</span> {{ $assistance->date_received_request ?? '-' }}</div>
                </div>
            @endforeach
        @else
            <p class="text-muted text-center">No assistance records available.</p>
        @endif


            <!-- edit modal -->
            <div id="editModal_{{ $client->id }}" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 1050; background: rgba(0,0,0,0.5);">
    <div class="bg-white" style="max-width: 800px; width: 100%; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 2rem; max-height: 90vh; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold text-success mb-0 bg-success bg-opacity-10 rounded py-2 px-3 w-100 text-center">Edit Information</h2>
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
                                                class="form-check-input" id="sector_{{ $client->id }}_{{ $sector->id }}"
                                                {{ $client->vulnerabilitySectors->contains($sector->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sector_{{ $client->id }}_{{ $sector->id }}">{{ $sector->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Assistance Type -->
                                <div class="mb-3">
                                    <label class="form-label">Assistance Type:</label>
                                    <select name="assistance_type_id" id="edit_assistance_type" class="form-select" required>
                                        <option disabled selected>Select type</option>
                                        @foreach ($assistanceTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $assistance->assistance_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Assistance Categories:</label>
                                    <div id="categories_section" class="ps-3">
                                        <p class="text-muted">Please select an assistance type to view categories.</p>
                                    </div>
                                </div>
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
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn bg-success" style=" color: #fff; border: none;">Save Changes</button>
                            <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary" style="border: none;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
    </div>

</div>


{{-- Claim Info Card --}}

<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-success custom-green-btn rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm"
        data-bs-toggle="modal" data-bs-target="#editClaimModal">
        <i class="fas fa-edit"></i>
        <span class="d-none d-md-inline">Update</span>
    </button>
</div>
    @php
        $assistance = $client->assistances->first();
        $claim = $assistance ? \App\Models\Claim::where('client_assistance_id', $assistance->id)->first() : null;
    @endphp

    
         <div class="card border-0 shadow-sm p-4 mb-4">
        <h4 class="fw-bold text-success text-center mb-4 bg-success bg-opacity-10 rounded py-2">Claim Information</h4>
        @if ($claim)
        
            <!-- Update Claim Modal -->
<div class="modal fade" id="editClaimModal" tabindex="-1" aria-labelledby="editClaimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
            <form action="{{ $claim ? route('claims.update', $claim->id) : '#' }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="client_assistance_id" value="{{ $assistance->id ?? '' }}">

                <div class="modal-header border-0 pb-0" style="background: none;">
                    <h5 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" id="editClaimModalLabel" style="font-size: 1.5rem;">Update Claim Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body row g-4 pt-3">
                    <div id="reason-container" style="display: {{ $claim && $claim->status === 'disapproved' ? 'block' : 'none' }};">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason of Disapproval:</label>
                            <input type="text" name="reason_of_disapprovement" class="form-control" value="{{ old('reason_of_disapprovement', $claim->reason_of_disapprovement) }}">
                        </div>
                    </div> 

                    <div class="col-md-6">
                        <label class="form-label">CAFOA Prepared Date</label>
                        <input type="date" class="form-control" name="date_cafoa_prepared" value="{{ $claim->date_cafoa_prepared }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">PGO Received Date</label>
                        <input type="date" class="form-control" name="date_pgo_received" value="{{ $claim->date_pgo_received }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" name="amount_approved" value="{{ $claim->amount_approved }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Preferred Payment Method</label>
                        <select name="form_of_payment" class="form-select">
                            <option value="">Select</option>
                            <option value="cash" {{ $claim->form_of_payment === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="cheque" {{ $claim->form_of_payment === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>

                    <div id="check-no-field" class="col-md-6" style="display: none;">
                        <label for="check_no" class="form-label">Check Number</label>
                        <input type="text" name="check_no" id="check_no" class="form-control" value="{{ old('check_no', $claim->checkPayment->check_no ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Payout Date</label>
                        <input type="date" class="form-control" name="payout_date" value="{{ $claim->payout_date }}">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2" style="background: none;">
                    <button type="submit" class="btn btn-success custom-green-btn rounded-pill px-4">Save Changes</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


        @endif
        <div class="row g-3 mb-2">
            <div class="col-md-6 d-flex align-items-center">
                <span class="fw-semibold">Status:</span>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 ms-2">{{ ucfirst(optional($claim)->status ?? 'Pending') }}</span>
            </div>
            @if ($claim && $claim->status === 'disapproved')
                <div class="col-md-6 d-flex align-items-center">
                    <span class="fw-semibold">Reason of Disapproval:</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 ms-2">{{ $claim->reason_of_disapprovement ?? '-' }}</span>
                </div>
            @endif
        </div>
        <div class="row g-3">
            <div class="col-md-4"><span class="fw-semibold">CAFOA Prepared Date:</span> {{ optional($claim)->date_cafoa_prepared ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">PGO Received Date:</span> {{ optional($claim)->date_pgo_received ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Amount:</span> {{ optional($claim)->amount_approved ?? '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Preferred Payment Method:</span> {{ optional($claim)->form_of_payment ? ucfirst($claim->form_of_payment) : '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">Payout Date:</span> {{ optional($claim)->payout_date ? \Carbon\Carbon::parse($claim->payout_date)->format('F d, Y') : '-' }}</div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openEditModal(clientId) {
        document.getElementById(`editModal_${clientId}`).classList.remove('d-none');
    }

    function closeEditModal(clientId) {
        document.getElementById(`editModal_${clientId}`).classList.add('d-none');
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
        const modal = document.querySelector('#editClaimModal');

        if (modal) {
            const paymentSelect = modal.querySelector('select[name="form_of_payment"]');
            const checkNoField = modal.querySelector('#check-no-field');

            function toggleCheckNoField() {
                if (paymentSelect && checkNoField) {
                    if (paymentSelect.value === 'cheque') {
                        checkNoField.style.display = 'block';
                    } else {
                        checkNoField.style.display = 'none';
                    }
                }
            }

            toggleCheckNoField(); // Run when modal loads
            paymentSelect.addEventListener('change', toggleCheckNoField); // Trigger when changed
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const assistanceTypeSelect = document.getElementById('edit_assistance_type');
        const medicalCaseSection = document.getElementById('medical_case_section');
        const categoriesSection = document.getElementById('categories_section');
        const requirementsSection = document.getElementById('requirements_section');

        function toggleMedicalSection() {
            const selectedText = assistanceTypeSelect.options[assistanceTypeSelect.selectedIndex].text.toLowerCase();
            if (selectedText.includes('medical')) {
                medicalCaseSection.style.display = 'block';
            } else {
                medicalCaseSection.style.display = 'none';
            }
        }

        function loadCategoriesAndRequirements(typeId) {
            // Categories
            fetch(`/get-categories/${typeId}`)
                .then(res => res.json())
                .then(data => {
                    let html = data.length ? '' : '<p class="text-muted">No categories found.</p>';
                    // Get the current selected category from the server-rendered value
                    let selectedCategory = null;
                    try {
                        selectedCategory = document.querySelector('input[name="assistance_category_id"]:checked')?.value || "{{ $assistance->assistance_category_id ?? '' }}";
                    } catch(e) { selectedCategory = "{{ $assistance->assistance_category_id ?? '' }}"; }
                    data.forEach(c => {
                        const checked = selectedCategory == c.id ? 'checked' : '';
                        html += `<div class="form-check">
                                    <input type="radio" name="assistance_category_id" value="${c.id}" class="form-check-input category-radio" id="edit-category-${c.id}" ${checked}>
                                    <label class="form-check-label" for="edit-category-${c.id}">${c.category_name}</label>
                                </div>`;
                    });
                    categoriesSection.innerHTML = html;
                });
            // Requirements
            fetch(`/get-requirements/${typeId}`)
                .then(res => res.json())
                .then(data => {
                    let html = data.length ? '' : '<p class="text-muted">No requirements found.</p>';
                    data.forEach(r => {
                        html += `<div class="form-check">
                                    <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input requirement-checkbox">
                                    <label class="form-check-label">${r.requirement_name}</label>
                                </div>`;
                    });
                    requirementsSection.innerHTML = html;
                });
        }

        if (assistanceTypeSelect) {
            assistanceTypeSelect.addEventListener('change', function () {
                toggleMedicalSection();
                loadCategoriesAndRequirements(this.value);
            });
            // Initial load if pre-selected
            toggleMedicalSection();
            if (assistanceTypeSelect.value) {
                loadCategoriesAndRequirements(assistanceTypeSelect.value);
            }
        }
    });
    

</script>
@endsection

