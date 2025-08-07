@extends('layouts.app')

@section('content')

<main class="flex-1 p-4 overflow-auto">
    <h1 class="fs-3 fw-bold text-dark mb-4">Assistance Management</h1>

    <div class="clients-container mb-4 d-flex align-items-center justify-content-between" style="background: none; box-shadow: none; padding: 0;">
        <div>
            <button class="btn btn-success custom-green-btn rounded px-4 py-2 fw-semibold shadow-sm me-2" style="border-radius: 0.7rem; font-size: 1rem; background-color: #198754; border: none;" id="addAssistanceBtn">
                <i class="fas fa-plus me-2"></i> Add Assistance
            </button>
        </div>
        <form id="searchForm" method="GET" action="{{ route('clients.assistance') }}" class="d-flex">
            <input type="text" name="search" id="searchInput" class="form-control me-2" autocomplete="off"
                value="{{ request('search') }}" placeholder="Search by name">
            <button type="submit" class="btn btn-success">Search</button>
        </form>

    </div>

    <div class="table-responsive bg-white p-3 pb-1 rounded shadow-sm">
        <table class="table table-hover" style="width:100%; background: transparent; border: none; font-family: 'Inter', sans-serif;">
            <thead class="bg-white" style="border: none;">
    <tr style="border: none;">
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Client Name</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Sex</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Age</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Contact Number</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Representative</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Representative Contact</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Municipality</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Medical Case</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Assistance Type</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Assistance Category</th>
        <th class="assistance-th bg-success bg-opacity-10" style="color: #374151; font-weight: 600;">Status</th>
        <th class="assistance-th text-center bg-success bg-opacity-10" style="width: 70px; color: #374151; font-weight: 600;">Actions</th>
    </tr>
    </thead>
            <tbody>
                @forelse($assistances as $assistance)
                    @php
                        $client = $assistance->client;
                        $claim = $assistance ? \App\Models\Claim::where('client_assistance_id', $assistance->id)->first() : null;
                        $status = $claim->status ?? 'pending';
                    @endphp
                    <tr style="border: none; font-family: 'Inter', sans-serif;">
                        <td class="assistance-td">{{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}</td>
                        <td class="assistance-td">{{ $client->sex }}</td>
                        <td class="assistance-td">{{ $client->age }}</td>
                        <td class="assistance-td">{{ $client->contact_number }}</td>
                        <td class="assistance-td">{{ $assistance->payee && !empty($assistance->payee->full_name) ? $assistance->payee->full_name : '-' }}</td>
                        <td class="assistance-td">{{ optional($assistance->payee)->contact_number ?? '-' }}</td>
                        <td class="assistance-td">{{ $client->municipality->name ?? '-' }}</td>
                        <td class="assistance-td">{{ $assistance->medical_case ?? '-' }}</td>
                        <td class="assistance-td">{{ $assistance->assistanceType->type_name ?? '-' }}</td>
                        <td class="assistance-td" style="text-decoration: none;">
                            @if($assistance->assistanceCategory->category_name === 'Others' && $assistance->other_category_name)
                                {{ $assistance->other_category_name }}
                            @else
                                {{ $assistance->assistanceCategory->category_name }}
                            @endif
                        </td>



                        <td class="assistance-td">
                            <span class="fw-semibold {{ $status === 'approved' ? 'text-success' : ($status === 'disapproved' ? 'text-danger' : 'text-warning') }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="text-center assistance-td" style="width: 70px;">
                            <div class="d-flex justify-content-center gap-1 assistance-actions" style="border: none;">
                                <div class="dropdown">
                                    <a href="#" role="button" id="dropdownMenu{{ $client->id }}" data-bs-toggle="dropdown" aria-expanded="false" class="btn p-1" style="border: none; background: none; color: #76AE91;" title="Status">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $client->id }}" style="background-color: var(--mint-green-500); border-radius: 0.5rem;">

                                        @php
                                            $claim = \App\Models\Claim::where('client_assistance_id', $assistance->id)->first();
                                        @endphp

                                        @if($claim)
                                            <li>
                                                <form action="{{ route('claims.update-status', $claim->id) }}" method="POST" style="border: none;">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="dropdown-item" style="color: #76AE91; font-weight: 500; border: none;">
                                                        <i class="fas fa-check me-2"></i> Approve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('claims.update-status', $claim->id) }}" method="POST" style="border: none;">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="disapproved">
                                                    <button type="submit" class="dropdown-item" style="color: #e74c3c; font-weight: 500; border: none;">
                                                        <i class="fas fa-times me-2"></i> Disapprove
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('claims.update-status', $claim->id) }}" method="POST" style="border: none;">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="submit" class="dropdown-item" style="color: #ffc107; font-weight: 500; border: none;">
                                                        <i class="fas fa-clock me-2"></i> Pending
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- History icon -->
                                <button type="button" class="btn p-1 ms-2" data-bs-toggle="modal" data-bs-target="#historyModal_{{ $client->id }}" style="border: none; background: none; color: #4A7C65;" title="View History">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>

                                <!-- View Client Information & Claim Information icon -->
                                <a href="{{ route('clients.show', $client->id) }}" class="btn p-1" style="border: none; background: none; color: 	#5B7B8A;" title="View Client">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="12" class="text-muted assistance-td" style="font-family: 'Inter', sans-serif;">No clients found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            {{-- Pagination Links --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="mb-0 text-muted small">
                        Showing {{ $assistances->firstItem() }} to {{ $assistances->lastItem() }} of {{ $assistances->total() }} results
                    </p>

                    <ul class="pagination pagination-sm mb-0">
                        
                        @if ($assistances->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Prev</span></li>
                        @else
                            <li class="page-item"><a href="{{ $assistances->previousPageUrl() }}" class="page-link">Prev</a></li>
                        @endif

                        
                        @for ($i = 1; $i <= $assistances->lastPage(); $i++)
                            <li class="page-item {{ $assistances->currentPage() == $i ? 'active' : '' }}">
                                <a href="{{ $assistances->url($i) }}" class="page-link">{{ $i }}</a>
                            </li>
                        @endfor

                        
                        @if ($assistances->hasMorePages())
                            <li class="page-item"><a href="{{ $assistances->nextPageUrl() }}" class="page-link">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </div>



            @foreach($assistances as $assistance)
    @php $client = $assistance->client; @endphp
    <div class="modal fade" id="historyModal_{{ $client->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">History - {{ $client->first_name }} {{ $client->last_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @php
                        // ✅ Get all claims for this client
                        $claims = \App\Models\Claim::where('client_id', $client->id)
                            ->with(['clientAssistance.assistanceType', 'disbursement'])
                            ->orderBy('created_at', 'desc')
                            ->get();

                        // ✅ Separate: 
                        // 1️⃣ Claims WITH disbursement
                        $claimsWithDisbursement = $claims->filter(fn($c) => $c->disbursement);

                        // 2️⃣ Claims WITHOUT disbursement BUT disapproved
                        $disapprovedWithoutDisbursement = $claims->filter(fn($c) => !$c->disbursement && $c->status === 'disapproved');
                    @endphp

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Assistance Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Show all claims WITH disbursement --}}
                            @foreach($claimsWithDisbursement as $claim)
                                <tr>
                                    <td>{{ $claim->clientAssistance?->assistanceType?->type_name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($claim->disbursement->amount ?? 0, 2) }}</td>
                                    <td>
                                        @if($claim->disbursement?->claim_status === 'claimed')
                                            <span class="badge bg-success">Claimed</span>
                                        @else
                                            {{ ucfirst($claim->disbursement->claim_status ?? $claim->status) }}
                                        @endif
                                    </td>
                                    <td>{{ $claim->disbursement->created_at?->format('M d, Y') }}</td>
                                </tr>
                            @endforeach


                            {{-- Show only disapproved claims WITHOUT disbursement --}}
                            @foreach($disapprovedWithoutDisbursement as $claim)
                                <tr>
                                    <td>{{ $claim->clientAssistance?->assistanceType?->type_name ?? 'N/A' }}</td>
                                    <td>-</td>
                                    <td class="text-danger fw-bold">Disapproved</td>
                                    <td>{{ $claim->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endforeach

                            {{-- Show empty row if nothing --}}
                            @if($claimsWithDisbursement->isEmpty() && $disapprovedWithoutDisbursement->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No history yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endforeach

</main>

    


    

                
                <!-- Add Assistance Modal -->

    <div id="addAssistanceModal" class="modal d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center modal-overlay" tabindex="-1" aria-labelledby="addAssistanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form id="assistanceForm" method="POST" action="{{ route('client-assistances.store') }}" autocomplete="off">
                @csrf
                <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                    <div class="modal-header border-0 pb-0" style="background: none;">
                        <h2 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" style="font-size: 1.5rem;">Add Assistance</h2>
                        <button type="button" class="btn-close" onclick="closeAssistanceModal()"></button>
                    </div>
                    <div class="modal-body row g-4 pt-3">
                        <div class="col-md-6">
                            <label for="search_client_input" class="form-label fw-semibold">Search Client</label>
                            <input type="text" id="search_client_input" class="form-control rounded border border-success bg-light" placeholder="Type client name..." autocomplete="off" style="box-shadow: none;">
                            <input type="hidden" name="client_id" id="modal_client_id">
                            <div id="client_search_results" class="list-group position-absolute w-100 z-3" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_received_request" class="form-label fw-semibold">Date Received</label>
                            <input type="date" name="date_received_request" id="date_received_request" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="has_representative" id="has_representative" class="form-check-input" value="1" onchange="toggleRepresentativeFields()">
                                <label class="form-check-label" for="has_representative">Client has a Representative?</label>
                            </div>
                            <div id="representativeFields" style="display: none;">
                                <div class="form-group mb-2">
                                    <label>Representative First Name</label>
                                    <input type="text" name="representative_first_name" class="form-control rounded border border-success bg-light" autocomplete="off" style="box-shadow: none;">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Representative Middle Name</label>
                                    <input type="text" name="representative_middle_name" class="form-control rounded border border-success bg-light" autocomplete="off" style="box-shadow: none;">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Representative Last Name</label>
                                    <input type="text" name="representative_last_name" class="form-control rounded border border-success bg-light" autocomplete="off" style="box-shadow: none;">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Relationship to the Client</label>
                                    <input type="text" name="relationship" class="form-control rounded border border-success bg-light" autocomplete="off" style="box-shadow: none;">
                                </div>
                                <div class="form-group mb-2">
                                    <label>Representative Contact Number</label>
                                    <input type="text" name="representative_contact_number" class="form-control rounded border border-success bg-light" autocomplete="off" style="box-shadow: none;">
                                </div>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="proof_of_relationship" id="proof_of_relationship" class="form-check-input" value="1">
                                    <label for="proof_of_relationship" class="form-check-label">Proof of Relationship Provided?</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assistance Type</label>
                            <select name="assistance_type_id" id="assistance_type" class="form-control rounded border border-success bg-light" required style="box-shadow: none;">
                                <option value="">Select Assistance Type</option>
                                @foreach($assistanceTypes as $type)
                                    <option value="{{ $type->id }}" data-name="{{ strtolower($type->type_name) }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="medical_case_section" style="display: none;">
                            <label class="form-label fw-semibold">Medical Case</label>
                            <select name="medical_case" class="form-control rounded border border-success bg-light" style="box-shadow: none;">
                                <option value="">Select Case</option>
                                <option value="CKD">CKD</option>
                                <option value="Cancer">Cancer</option>
                                <option value="Heart Illness">Heart Illness</option>
                                <option value="Diabetes">Diabetes</option>
                                <option value="Hypertension">Hypertension</option>
                                <option value="Others">Others</option>
                            </select>
                            <div id="other_medical_case_input" class="mt-2 d-none">
                                <label for="other_case" class="form-label">Please specify:</label>
                                <input type="text" name="other_case" id="other_case" class="form-control" placeholder="Enter other medical case">
                            </div>
                        </div>
                        <!-- Category Section -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <div id="categories_section" class="ps-3">
                                <p class="text-muted">Please select an assistance type to view categories.</p>
                            </div>
                            <div id="other_category_input" class="mt-2" style="display: none;">
                                <input type="text" name="other_category" class="form-control" placeholder="Please specify other category">
                            </div>
                        </div>

                        <!-- Requirements Section -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Requirements</label>
                            <div id="requirements_section" class="ps-3">
                                <p class="text-muted">Please select an assistance type to view requirements.</p>
                            </div>

                            <!-- "Others" input field (optional) -->
                            <div id="other_requirement_input" class="mt-2" style="display: none;">
                                <input type="text" name="other_requirement" class="form-control" placeholder="(Optional) Specify other requirement">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2" style="background: none;">
                        <button type="submit" class="btn btn-success custom-green-btn rounded-pill px-4" id="saveAssistanceBtn" disabled>Save</button>
                        <button type="button" class="btn btn-secondary rounded-pill px-4" onclick="closeAssistanceModal()">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



</script>
@section('scripts')
<script>
function openAssistanceModalFromSearch() {
    const modal = new bootstrap.Modal(document.getElementById('addAssistanceModal'));
    document.getElementById('search_client_input').value = '';
    document.getElementById('modal_client_id').value = '';
    document.getElementById('client_search_results').innerHTML = '';
    modal.show();
}

function closeAssistanceModal() {
    document.getElementById('addAssistanceModal').classList.add('d-none');
}

function toggleRepresentativeFields() {
    const checkbox = document.getElementById('has_representative');
    const repFields = document.getElementById('representativeFields');
    if (checkbox && repFields) {
        repFields.style.display = checkbox.checked ? 'block' : 'none';
    }
}

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

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addAssistanceBtn')?.addEventListener('click', function () {
        document.getElementById('addAssistanceModal').classList.remove('d-none');
    });

    // Auto-submit on typing
    document.getElementById('searchInput').addEventListener('input', function() {
        document.getElementById('searchForm').submit(); // Auto-submit on typing
    });
    toggleRepresentativeFields();
    document.getElementById('has_representative')?.addEventListener('change', toggleRepresentativeFields);

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

    const dateInput = document.getElementById('date_received_request');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
    }

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

    // Clear sections if no valid type is selected  
    if (!typeId) {
        document.getElementById('requirements_section').innerHTML = '<p class="text-muted">Please select an assistance type to view requirements.</p>';
        document.getElementById('categories_section').innerHTML = '<p class="text-muted">Please select an assistance type to view categories.</p>';
        attachValidation();
        return;
    }
    

    // fetch the requirements
    fetch(`/get-requirements/${typeId}`)
        .then(res => res.json())
        .then(data => {
            // ✅ Sort so "Others" is always last
            data.sort((a, b) => {
                if (a.requirement_name.toLowerCase().includes("others")) return 1;
                if (b.requirement_name.toLowerCase().includes("others")) return -1;
                return 0;
            });

            let html = data.length
                ? `<div class="form-check mb-2">
                        <input type="checkbox" id="check_all_requirements" class="form-check-input">
                        <label class="form-check-label fw-semibold" for="check_all_requirements">Check All Requirements</label>
                </div><ol class="ps-3">`
                : '<p>No requirements found.</p>';

            data.forEach(r => {
                html += `<li>
                            <div class="form-check">
                                <input type="checkbox" name="requirements[]" value="${r.id}" class="form-check-input requirement-checkbox">
                                <label class="form-check-label">${r.requirement_name}</label>
                            </div>
                        </li>`;
            });

            html += data.length ? `</ol>` : '';

            const requirementsSection = document.getElementById('requirements_section');
            if (requirementsSection) {
                requirementsSection.innerHTML = html;

                const checkAll = document.getElementById('check_all_requirements');
                const checkboxes = requirementsSection.querySelectorAll('.requirement-checkbox');

                if (checkAll) {
                    checkAll.addEventListener('change', function () {
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;

                            // ✅ Delay dispatch so the checked state is updated first
                            setTimeout(() => {
                                cb.dispatchEvent(new Event('change'));
                            }, 0);
                        });

                        if (typeof validate === 'function') validate();
                    });

                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', function () {
                            const allChecked = [...checkboxes].every(cb => cb.checked);
                            checkAll.checked = allChecked;
                            if (typeof validate === 'function') validate();
                        });
                    });
                }


                // ✅ Show/hide "Others" input field dynamically
                const otherInput = document.getElementById('other_requirement_input');
                if (otherInput) {
                    otherInput.style.display = "none";
                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', function () {
                            const labelText = this.nextElementSibling.textContent.trim().toLowerCase();
                            if (labelText.includes("others") && this.checked) {
                                otherInput.style.display = "block";
                            } else if (labelText.includes("others") && !this.checked) {
                                otherInput.style.display = "none";
                                otherInput.querySelector("input").value = "";
                            }
                        });
                    });
                }

                if (typeof attachValidation === 'function') attachValidation();
            }
        })
        .catch(err => {
            console.error('Error fetching requirements:', err);
        });



    // Fetch and populate categories
fetch(`/get-categories/${typeId}`)
    .then(res => res.json())
    .then(data => {
        let html = data.length
            ? ''
            : '<p class="text-muted">No categories found.</p>';

        data.forEach(c => {
            const isOthers = c.category_name.toLowerCase() === "others";
            html += `
                <div class="form-check">
                    <input type="radio" 
                           name="assistance_category_id" 
                           value="${c.id}" 
                           class="form-check-input category-radio ${isOthers ? 'others-radio' : ''}" 
                           id="category-${c.id}">
                    <label class="form-check-label" for="category-${c.id}">${c.category_name}</label>
                </div>`;
        });

        const categoriesSection = document.getElementById('categories_section');
        if (!categoriesSection) return;

        categoriesSection.innerHTML = html;

        const otherCategoryInput = document.getElementById("other_category_input");
        if (!otherCategoryInput) return;

        otherCategoryInput.style.display = "none";

        const othersRadio = categoriesSection.querySelector(".others-radio");

        // Show input when "Others" is clicked
        if (othersRadio) {
            othersRadio.addEventListener("change", function () {
                if (this.checked) {
                    otherCategoryInput.style.display = "block";
                }
            });
        }

        // Hide input when any non-"Others" radio is clicked
        categoriesSection.querySelectorAll(".category-radio").forEach(radio => {
            radio.addEventListener("change", function () {
                if (!this.classList.contains("others-radio")) {
                    otherCategoryInput.style.display = "none";
                    otherCategoryInput.querySelector("input").value = "";
                }
            });
        });

        // ✅ Check immediately if "Others" is already selected (important for edit forms)
        if (othersRadio && othersRadio.checked) {
            otherCategoryInput.style.display = "block";
        }

        if (typeof attachValidation === 'function') attachValidation();
    })
    .catch(err => {
        console.error('Error fetching categories:', err);
        document.getElementById('categories_section').innerHTML =
            '<p class="text-danger">Failed to load categories.</p>';
    });


    });


    function validate() {
        const clientSelected = clientIdField.value.trim() !== '';
        const categorySelected = document.querySelector('.category-radio:checked') !== null;

        // ✅ Get all requirement checkboxes
        const checkboxes = [...document.querySelectorAll('.requirement-checkbox')];

        // ✅ Ignore "Others" in validation
        const normalReqs = checkboxes.filter(cb => 
            !cb.nextElementSibling.textContent.trim().toLowerCase().includes("others")
        );

        // ✅ Check if all normal requirements are checked
        const allNormalChecked = normalReqs.every(cb => cb.checked);

        const noRequirements = checkboxes.length === 0;

        // ✅ Enable save if client + category selected + all normal checked (others optional)
        saveBtn.disabled = !(clientSelected && categorySelected && (noRequirements || allNormalChecked));
    }

    });
</script>
@endsection
@endsection