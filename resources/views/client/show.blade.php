@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <button 
            onclick="openEditModal({{ $client->id }})" 
            class="btn btn-success custom-green-btn rounded-circle p-2 shadow-sm" 
            title="Edit Client">
            <i class="fas fa-edit"></i>
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
            <div class="col-md-4"><span class="fw-semibold">Valid ID Presented?</span> {{ $client->valid_id ? 'Yes' : 'No' }}</div>
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
        @php
    $latestAssistance = $client->assistances()->latest()->first();
@endphp

@if ($latestAssistance)
    <div class="row g-3 mb-2">
        <div class="col-md-4"><span class="fw-semibold">Assistance Type:</span> {{ $latestAssistance->assistanceType->type_name ?? '-' }}</div>
        <div class="col-md-4"><span class="fw-semibold">Assistance Category:</span> {{ $latestAssistance->assistanceCategory->category_name ?? '-' }}</div>
        <div class="col-md-4"><span class="fw-semibold">Medical Case:</span> {{ $latestAssistance->medical_case ?? '-' }}</div>
        <div class="col-md-4"><span class="fw-semibold">Date Received:</span> {{ $latestAssistance->date_received_request ?? '-' }}</div>
        <div class="col-md-4">
            <span class="fw-semibold">Created By:</span> 
             {{ $latestAssistance->createdByEmployee ? $latestAssistance->createdByEmployee->full_name : 'N/A' }}
        </div>
    </div>
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
        <form action="{{ route('clients.updateAssistance', $client->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                            <!-- Assistance Type -->
                                <div class="mb-3">
                                    <label class="form-label">Assistance Type:</label>
                                    <select name="assistance_type_id" id="edit_assistance_type" class="form-select" required>
                                        <option disabled selected>Select type</option>
                                        @foreach ($assistanceTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $latestAssistance && $latestAssistance->assistance_type_id == $type->id ? 'selected' : '' }}>
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
                                <div class="col-md-6" id="medical_case_section" style="display: none;">
                                    <label class="form-label fw-semibold">Medical Case</label>
                                    <select name="medical_case" class="form-control rounded border border-success bg-light" style="box-shadow: none;">
                                        <option value="" {{ empty($latestAssistance->medical_case) ? 'selected' : '' }}>Select Case</option>
                                        <option value="CKD" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'CKD') ? 'selected' : '' }}>CKD</option>
                                        <option value="Cancer" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'Cancer') ? 'selected' : '' }}>Cancer</option>
                                        <option value="Heart Illness" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'Heart Illness') ? 'selected' : '' }}>Heart Illness</option>
                                        <option value="Diabetes" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'Diabetes') ? 'selected' : '' }}>Diabetes</option>
                                        <option value="Hypertension" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'Hypertension') ? 'selected' : '' }}>Hypertension</option>
                                        <option value="Others" {{ (isset($latestAssistance->medical_case) && $latestAssistance->medical_case == 'Others') ? 'selected' : '' }}>Others</option>
                                    </select>

                                    <div id="other_medical_case_input" class="mt-2 d-none">
                                        <label for="other_case" class="form-label">Please specify:</label>
                                        <input type="text" name="other_case" id="other_case" class="form-control" placeholder="Enter other medical case" >
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
@if ($claim && in_array($claim->status, ['approved', 'disapproved']))
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-success custom-green-btn rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm"
        data-bs-toggle="modal" data-bs-target="#editClaimModal">
        <i class="fas fa-edit"></i>
        <span class="d-none d-md-inline">Update</span>
    </button>
</div>
@endif

    @php
        $assistance = $client->assistances()->latest()->first();
        $claim = $assistance ? \App\Models\Claim::where('client_assistance_id', $assistance->id)->first() : null;
    @endphp

    
         <div class="card border-0 shadow-sm p-4 mb-4">
        <h4 class="fw-bold text-success text-center mb-4 bg-success bg-opacity-10 rounded py-2">Claim Information</h4>
        @if ($claim)
        
            @php
    $latestAssistance = $client->assistances()->latest()->first();
    $claim = $latestAssistance 
        ? \App\Models\Claim::where('client_assistance_id', $latestAssistance->id)->first()
        : null;
@endphp

{{-- Update Claim Modal --}}
@if($latestAssistance)
<div class="modal fade" id="editClaimModal" tabindex="-1" aria-labelledby="editClaimModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
            <form action="{{ $claim ? route('claims.update', $claim->id) : '#' }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="client_assistance_id" value="{{ $latestAssistance->id }}">

                {{-- Reason of Disapproval --}}
                <div id="reason-container" style="display: {{ $claim && $claim->status === 'disapproved' ? 'block' : 'none' }};">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason of Disapproval:</label>
                        <textarea name="reason_of_disapprovement" class="form-control" autocomplete="off" rows="3" style="white-space: normal; word-break: break-word;">
                            {{ old('reason_of_disapprovement', $claim->reason_of_disapprovement) }}
                        </textarea>
                    </div>
                </div>


                {{-- Other fields (hidden if disapproved) --}}
                <div id="approved-fields-container" style="display: {{ $claim && $claim->status === 'disapproved' ? 'none' : 'flex' }}; flex-wrap: wrap; gap: 1rem;">
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
                        <label for="source_of_fund" class="form-label">Source of Funds</label>
                        <select name="source_of_fund" id="source_of_fund" class="form-select">
                            <option value="">Select Source of Fund</option>
                            <option value="Regular" {{ $claim && $claim->source_of_fund === 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Senior" {{ $claim && $claim->source_of_fund === 'Senior' ? 'selected' : '' }}>Senior Citizen</option>
                            <option value="PDRRM" {{ $claim && $claim->source_of_fund === 'PDRRM' ? 'selected' : '' }}>PDRRM</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <hr class="my-3" style="border-top: 4px solid red;">
                    </div>

                    <p><strong>Note:</strong> To be Updated Once the Voucher is Received at the Provincial Treasury Office:</p>

                    <div class="col-md-6">
                        <label class="form-label">Preferred Payment Method</label>
                        <select name="form_of_payment" class="form-select">
                            <option value="">Select</option>
                            <option value="cash" {{ $claim && $claim->form_of_payment === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="cheque" {{ $claim && $claim->form_of_payment === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>

                    <div id="check-no-field" class="col-md-6" style="display: none;">
                        <label for="check_no" class="form-label">Check No.</label>
                        <input type="text" name="check_no" id="check_no" class="form-control"
                            value="{{ old('check_no', $claim?->checkPayment?->check_no ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Payout Date</label>
                        <input type="date" class="form-control" name="payout_date" value="{{ $claim->payout_date }}">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success custom-green-btn rounded-pill px-4">Save Changes</button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

        
        @if ($claim && in_array($claim->status, ['approved', 'disapproved']))
    <div class="col-md-4">
        <span class="fw-semibold">
            @if($claim->status === 'approved')
                Date Approved:
            @else
                Date Disapproved:
            @endif
        </span> 
        {{ $claim->date_status_updated 
            ? \Carbon\Carbon::parse($claim->date_status_updated)->format('F d, Y') 
            : '-' 
        }}
    </div>
@endif


        @endif
        <div class="row g-3 mb-2">
            <div class="col-md-6 d-flex align-items-center">
                <span class="fw-semibold">Status:</span>
                @if($claim)
                    @php
                        $status = $claim->status;
                        $badgeClass = $status === 'approved'
                            ? 'bg-success bg-opacity-10 text-success'
                            : ($status === 'disapproved'
                                ? 'bg-danger bg-opacity-10 text-danger'
                                : 'bg-warning bg-opacity-10 text-warning');
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2 ms-2">
                        {{ ucfirst($status) }}
                    </span>
                @else
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 ms-1">
                        No Claim Yet
                    </span>
                @endif
            </div>
            @if ($claim && $claim->status === 'disapproved')
                <div class="col-md-6 d-flex align-items-start">
                    <span class="fw-semibold mt-1">Reason of Disapproval:</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 ms-2" 
                        style="white-space: normal; word-break: break-word;">
                        {{ $claim->reason_of_disapprovement ?? '-' }}
                    </span>
                </div>
            @endif

        </div>
        <div class="row g-3">
            <div class="col-md-4"><span class="fw-semibold">CAFOA Prepared Date:</span> {{ optional($claim)->date_cafoa_prepared ? \Carbon\Carbon::parse($claim->date_cafoa_prepared)->format('F d, Y') : '-' }}</div>
            <div class="col-md-4"><span class="fw-semibold">PGO Received Date:</span> {{ optional($claim)->date_pgo_received ? \Carbon\Carbon::parse($claim->date_pgo_received)->format('F d, Y') : '-' }}</div>
            <div class="col-md-4" id="source-of-fund-display"><span class="fw-semibold">Source of Funds:</span> {{ optional($claim)->source_of_fund ?? '-' }}</div>
            <div class="col-md-4" id="amount-display">
                <span class="fw-semibold">Amount:</span>
                {{ isset($claim->amount_approved) ? '₱' . number_format($claim->amount_approved, 2) : '-' }}
            </div>

            <div class="col-md-4"><span class="fw-semibold">Preferred Payment Method:</span> {{ optional($claim)->form_of_payment ? ucfirst($claim->form_of_payment) : '-' }}</div>
            <div class="col-md-4">
                <span class="fw-semibold">Check Number:</span>
                {{ $claim?->checkPayment?->check_no ?? '-' }}
            </div>
            <div class="col-md-4"><span class="fw-semibold">Payout Date:</span> {{ optional($claim)->payout_date ? \Carbon\Carbon::parse($claim->payout_date)->format('F d, Y') : '-' }}</div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // PERSONAL INFO MODAL (open/close)
    function openEditModal(clientId) {
        document.getElementById(`editModal_${clientId}`).classList.remove('d-none');
    }

    function closeEditModal(clientId) {
        document.getElementById(`editModal_${clientId}`).classList.add('d-none');
    }

    // Representative Toggle (for personal info modal)
    function toggleRepresentativeFields() {
        const checkbox = document.getElementById('has_representative');
        const repFields = document.getElementById('representativeFields');
        if (repFields) {
            repFields.style.display = checkbox && checkbox.checked ? 'block' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleRepresentativeFields();
        document.getElementById('has_representative')?.addEventListener('change', toggleRepresentativeFields);
    });

    //amount and source of fund visibility based on assistance type
    // document.addEventListener("DOMContentLoaded", function () {
    //     const assistanceType = "{{ $assistance->assistanceType->type_name ?? '' }}";

    //     if (assistanceType.toLowerCase().includes("transportation")) {
    //         document.getElementById("amount-container").style.display = "none";
    //         document.getElementById("source-of-fund-container").style.display = "none";
    //     }

    // });

    // document.addEventListener("DOMContentLoaded", function () {
    //     const assistanceType = "{{ $claim->clientAssistance->assistanceType->type_name ?? '' }}";

    //     if (assistanceType.toLowerCase().includes("transportation")) {
    //         document.getElementById("source-of-fund-display").style.display = "none";
    //         document.getElementById("amount-display").style.display = "none";
    //     }
    // });


    // CLAIM MODAL (Update Claim Info)
    document.addEventListener("DOMContentLoaded", function () {
        const claimModal = document.querySelector("#editClaimModal");
        if (!claimModal) return;

        const paymentSelect = claimModal.querySelector('select[name="form_of_payment"]');
        const checkNoField = claimModal.querySelector("#check-no-field");
        const assistanceTypeSelect = claimModal.querySelector("#source_of_fund");
        const reasonContainer = claimModal.querySelector("#reason-container");
        const statusInput = claimModal.querySelector('select[name="status"]');

        // Toggle Check Number Field
        function toggleCheckNoField() {
            if (paymentSelect?.value === "cheque") {
                checkNoField.style.display = "block";
            } else {
                checkNoField.style.display = "none";
            }
        }
        paymentSelect?.addEventListener("change", toggleCheckNoField);
        toggleCheckNoField();

        // Toggle Reason of Disapproval
        if (statusInput && reasonContainer) {
            function toggleReasonField() {
                reasonContainer.style.display =
                    statusInput.value === "disapproved" ? "block" : "none";
            }
            statusInput.addEventListener("change", toggleReasonField);
            toggleReasonField();
        }
    });

    // ASSISTANCE CATEGORIES & REQUIREMENTS (Personal Info Modal)
    document.addEventListener('DOMContentLoaded', function () {
        const assistanceTypeSelect = document.getElementById('edit_assistance_type');
        const medicalCaseSection = document.getElementById('medical_case_section');
        const categoriesSection = document.getElementById('categories_section');
        const requirementsSection = document.getElementById('requirements_section');

        

        function toggleMedicalSection() {
            if (!assistanceTypeSelect) return;
            const selectedText = assistanceTypeSelect.options[assistanceTypeSelect.selectedIndex]?.text.toLowerCase() || '';
            medicalCaseSection.style.display = selectedText.includes('medical') ? 'block' : 'none';
        }

        function loadCategoriesAndRequirements(typeId) {
            if (!typeId) return;

            // Load Categories
            fetch(`/get-categories/${typeId}`)
                .then(res => res.json())
                .then(data => {
                    let html = data.length ? '' : '<p class="text-muted">No categories found.</p>';
                    let selectedCategory = document.querySelector('input[name="assistance_category_id"]:checked')?.value || "{{ $assistance->assistance_category_id ?? '' }}";
                    data.forEach(c => {
                        const checked = selectedCategory == c.id ? 'checked' : '';
                        html += `
                            <div class="form-check">
                                <input type="radio" name="assistance_category_id" value="${c.id}" class="form-check-input category-radio" id="edit-category-${c.id}" ${checked}>
                                <label class="form-check-label" for="edit-category-${c.id}">${c.category_name}</label>
                            </div>`;
                    });
                    categoriesSection.innerHTML = html;
                });

            // Load Requirements
            fetch(`/get-requirements/${typeId}`)
                .then(res => res.json())
                .then(data => {
                    let html = data.length ? '' : '<p class="text-muted">No requirements found.</p>';
                    data.forEach(r => {
                        html += `
                            <div class="form-check">
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

            toggleMedicalSection();
            if (assistanceTypeSelect.value) {
                loadCategoriesAndRequirements(assistanceTypeSelect.value);
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    const medicalCaseSelect = document.querySelector('select[name="medical_case"]');
    const otherCaseInputDiv = document.getElementById('other_medical_case_input');
    const otherCaseInput = document.getElementById('other_case');
    const form = document.querySelector('#addAssistanceForm');

    if (medicalCaseSelect) {
        medicalCaseSelect.addEventListener('change', function () {
            if (this.value === 'Others') {
                otherCaseInputDiv.classList.remove('d-none');
            } else {
                otherCaseInputDiv.classList.add('d-none');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function () {
            if (medicalCaseSelect.value === 'Others' && otherCaseInput.value.trim() !== '') {
                // Replace the select value before submit
                const customValue = otherCaseInput.value.trim();

                // Remove any old injected custom options first
                const existingCustom = medicalCaseSelect.querySelector('option[data-custom="true"]');
                if (existingCustom) existingCustom.remove();

                // Create a new option
                const newOption = new Option(customValue, customValue);
                newOption.selected = true;
                newOption.setAttribute('data-custom', 'true');
                medicalCaseSelect.add(newOption);
            }
        });
    }
});
</script>


@endsection

