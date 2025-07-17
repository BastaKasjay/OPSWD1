

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    
    

    <div class="card p-4 ">
        <div class="d-flex gap-2 justify-content-center">
        <button onclick="openEditModal({{ $client->id }})" class="btn btn-sm btn-warning" title="Edit Client">
            <i class="fas fa-edit"></i> Edit Personal Info
        </button>
    </div>
      
            <h4 class="fw-semibold mb-3 text-center">Personal Details</h4>
                <div class="row g-3">
                    <div class="col-md-4"><strong>First Name:</strong> {{ $client->first_name }}</div>
                    <div class="col-md-4"><strong>Middle Name:</strong> {{ $client->middle_name }}</div>
                    <div class="col-md-4"><strong>Last Name:</strong> {{ $client->last_name }}</div>
                    <div class="col-md-4"><strong>Municipality:</strong> {{ optional($client->municipality)->name ?? '-' }}</div>
                    <div class="col-md-4"><strong>Sex:</strong> {{ $client->sex }}</div>
                    <div class="col-md-4"><strong>Age:</strong> {{ $client->age }}</div>
                    <div class="col-md-4">
                        <strong>Birth Date:</strong>
                        {{ $client->birthday ? \Carbon\Carbon::parse($client->birthday)->format('F d, Y') : '-' }}
                    </div>

                    <div class="col-md-4"><strong>Contact:</strong> {{ $client->contact_number ?? '-' }}</div>
                    <div class="col-md-4">
                        <strong>Vulnerability Sectors:</strong>
                        {{ $client->vulnerabilitySectors->isNotEmpty()
                            ? $client->vulnerabilitySectors->pluck('name')->join(', ')
                            : 'None' }}
                    </div>
                </div>

       

            <h5  class="fw-semibold mb-3 text-center">Representative</h5>
            <div class="row g-3">
                <div class="col-md-4"><strong>First Name:</strong> {{ $client->payee->first_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Middle Name:</strong> {{ $client->payee->middle_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Last Name:</strong> {{ $client->payee->last_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Contact:</strong> {{ $client->payee->contact_number ?? '-' }}</div>
                <div class="col-md-4"><strong>Relationship:</strong> {{ $client->payee->relationship ?? '-' }}</div>
            </div>
   


            <h4 class="fw-semibold mb-3 text-center">Assistance</h4>
            @if ($client->assistances && $client->assistances->isNotEmpty())
                @foreach ($client->assistances as $assistance)
                    <div class="row g-3 border rounded mb-4 p-3">
                        <div class="col-md-4">
                            <strong>Assistance Type:</strong> {{ $assistance->assistanceType->type_name ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Assistance Category:</strong> {{ $assistance->assistanceCategory->category_name ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Medical Case:</strong> {{ $assistance->medical_case ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Date Received:</strong> {{ $assistance->date_received_request ?? '-' }}
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center">No assistance records available.</p>
            @endif


            <!-- edit modal -->
                    <div id="editModal_{{ $client->id }}" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50" style="z-index: 1050;">
                        <div class="bg-white rounded shadow-lg p-4 overflow-auto" style="max-width: 800px; max-height: 90vh; width: 100%;">
                            <h2 class="h4 fw-bold text-center text-mint-green mb-4">Edit Info</h2>

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
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
    </div>

    {{-- Claim Info Card --}}
    @php
        $assistance = $client->assistances->first();
        $claim = $assistance ? \App\Models\Claim::where('client_assistance_id', $assistance->id)->first() : null;
    @endphp

    <div class="card p-4 mt-4">
        <h4 class="text-center text-3xl fw-semibold my-4">Claim Information</h4>

        <!-- Update Button in top-right corner -->
        @if ($claim)
            <button class="btn btn-warning position-absolute top-0 end-0 m-3"
                    data-bs-toggle="modal" data-bs-target="#editClaimModal">
                Update
            </button>
            <!-- Update Claim Modal -->
            <div class="modal fade" id="editClaimModal" tabindex="-1" aria-labelledby="editClaimModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ $claim ? route('claims.update', $claim->id) : '#' }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="client_assistance_id" value="{{ $assistance->id ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="editClaimModalLabel">Edit Claim Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div id="reason-container" style="display: {{ $claim && $claim->status === 'disapproved' ? 'block' : 'none' }};">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason of Disapproval:</label>
                            <input type="text" name="reason_of_disapprovement" class="form-control" value="{{ old('reason_of_disapprovement', $claim->reason_of_disapprovement) }}">
                        </div>
                    </div> 


                    @if ($claim)
                    <div class="mb-3">
                    <label class="form-label">CAFOA Prepared Date</label>
                        <input type="date" class="form-control" name="date_cafoa_prepared" value="{{ $claim->date_cafoa_prepared }}">
                    </div>

                    <div class="mb-3">
                    <label class="form-label">PGO Received Date</label>
                        <input type="date" class="form-control" name="date_pgo_received" value="{{ $claim->date_pgo_received }}">
                    </div>

                    <div class="mb-3">
                    <label class="form-label">PTO Received Date</label>
                        <input type="date" class="form-control" name="date_pto_received" value="{{ $claim->date_pto_received }}">
                    </div>

                    <div class="mb-3">
                    <label class="form-label">Amount</label>
                        <input type="number" class="form-control" name="amount_approved" value="{{ $claim->amount_approved }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preferred Payment Method</label>
                            <select name="form_of_payment" class="form-select">
                                <option value="">Select</option>
                                <option value="cash" {{ $claim->form_of_payment === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ $claim->form_of_payment === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                    </div>

                    <div id="check-no-field" class="mb-3" style="display: none;">
                        <label for="check_no" class="form-label">Check Number</label>
                        <input type="text" name="check_no" id="check_no" class="form-control"
                            value="{{ old('check_no', $claim->checkPayment->check_no ?? '') }}">
                    </div>



                    <div class="mb-3">
                    <label class="form-label">Confirmation</label>
                        <input type="date" class="form-control" name="confirmation" value="{{ $claim->confirmation }}">
                    </div>

                    @else
                    <p class="text-muted">Claim record not available yet.</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if ($claim)
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    @endif
                </div>
                </form>
            </div>
            </div>

        @endif
<!-- ============Claim Information================== -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Status:</label>
            <input type="text" class="form-control" value="{{ ucfirst(optional($claim)->status ?? 'Pending') }}" readonly>
        </div>

        @if ($claim && $claim->status === 'disapproved')
            <div class="col-md-4">
                <label class="form-label fw-semibold">Reason of Disapproval:</label>
                <input type="text" class="form-control" value="{{ $claim->reason_of_disapprovement ?? '-' }}" readonly>
            </div>
        @endif


        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label fw-semibold">CAFOA Prepared Date:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->date_cafoa_prepared ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">PGO Received Date:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->date_pgo_received ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">PTO Received Date:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->date_pto_received ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Amount:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->amount_approved ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Preferred Payment Method:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->form_of_payment ? ucfirst($claim->form_of_payment) : '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Confirmation of Payee:</label>
                <input type="text" class="form-control" value="{{ optional($claim)->confirmation ?? '-' }}" readonly>
            </div>
        </div>
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
    

</script>
@endsection

