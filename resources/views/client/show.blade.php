

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    
    

    <div class="card p-4 ">

      
            <h4 class="fw-semibold mb-3 text-center">Personal Details</h4>
                <div class="row g-3">
                    <div class="col-md-4"><strong>First Name:</strong> {{ $client->first_name }}</div>
                    <div class="col-md-4"><strong>Middle Name:</strong> {{ $client->middle_name }}</div>
                    <div class="col-md-4"><strong>Last Name:</strong> {{ $client->last_name }}</div>
                    <div class="col-md-4"><strong>Municipality:</strong> {{ optional($client->municipality)->name ?? '-' }}</div>
                    <div class="col-md-4"><strong>Sex:</strong> {{ $client->sex }}</div>
                    <div class="col-md-4"><strong>Age:</strong> {{ $client->age }}</div>
                    <div class="col-md-4"><strong>Birth Date:</strong> {{ $client->birthday ?? '-' }}</div>
                    <div class="col-md-4"><strong>Contact:</strong> {{ $client->contact_number ?? '-' }}</div>
                </div>

       

            <h5  class="fw-semibold mb-3 text-center">Representative</h5>
            <div class="row g-3">
                <div class="col-md-4"><strong>First Name:</strong> {{ $client->payee->first_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Middle Name:</strong> {{ $client->payee->middle_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Last Name:</strong> {{ $client->payee->last_name ?? '-' }}</div>
                <div class="col-md-4"><strong>Contact:</strong> {{ $client->payee->contact_number ?? '-' }}</div>
                <div class="col-md-4"><strong>Relationship:</strong> {{ $client->payee->relationship ?? '-' }}</div>
            </div>
   


            <h5 class="fw-semibold mb-3 text-center">Assistance</h5>

            <div class="row ">
                <div class="col-md-4">
                    <h5 class="fw-semibold mb-2">Assistance Type:</h5>
                    <div>{{ optional($client->assistanceType)->type_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <h5 class="fw-semibold mb-2">Assistance Category:</h5>
                    <div>{{ optional($client->assistanceCategory)->category_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <h5 class="fw-semibold mb-2">Vulnerability Sectors:</h5>
                    @if ($client->vulnerabilitySectors->isNotEmpty())
                        <ul class="list-unstyled mb-0">
                            @foreach ($client->vulnerabilitySectors as $sector)
                                <li>{{ $sector->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0">None</p>
                    @endif
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

