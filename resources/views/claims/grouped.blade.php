@extends('layouts.app')

@section('content')
<main class="p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="fs-3 fw-bold mb-4 text-dark">Grouped Payouts</h1>
    <a id="downloadExcelBtn" href="#" class="btn btn-success btn-sm">
        <i class="fas fa-file-excel"></i> Download Excel
    </a>
    <a id="downloadPDFBtn" href="#" class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>


    @php
        $allClaims = collect($grouped)->flatten(2);
        $grandTotal = $allClaims
            ->filter(fn($claim) => $claim->disbursement?->claim_status === 'claimed')
            ->sum(fn($claim) => $claim->disbursement->total_amount_claimed ?? 0);

        // Get unique payment methods and payout dates for filters
        $methods = $allClaims->pluck('form_of_payment')->unique()->filter()->values();
        $dates = $allClaims->pluck('payout_date')->unique()->filter()->sort()->values();
    @endphp

    @if ($allClaims->isNotEmpty())
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter by Payment Method:</label>
                <select id="methodFilter" class="form-select">
                    <option value="">All Methods</option>
                    <option value="cash">Cash</option>
                    <option value="cheque">Cheque</option>
                </select>

            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter by Payout Date:</label>
                <select id="dateFilter" class="form-select">
                    <option value="">All Dates</option>
                    @foreach($dates as $date)
                        <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter by Status:</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="unclaimed">Unclaimed</option>
                    <option value="claimed">Claimed</option>
                </select>
            </div>
        </div>

        <div class="table-responsive bg-white p-1 rounded-lg shadow-md">
            <button id="openBatchEditModalBtn" class="btn btn-success mb-2"  data-bs-toggle="modal" data-bs-target="#batchEditModal" >
                Edit Selected Claims
            </button>

            <table class="table table-hover" id="claimsTable" style="width:100%; background: transparent; border: none;">
                <thead class="bg-success bg-opacity-10" style="border: none;">
                    <tr style="border: none;">
                        <th><input type="checkbox" id="select-all"></th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Client Name</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Representative</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Contact</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Municipality</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Amount Approved</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Date Received</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Claim Status</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Payment Method</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Check No.</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Payout Date</th>
                        <th class="fw-semibold text-success text-center" style="background: none; width: 70px; border: none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allClaims as $claim)
                        <tr 
                            data-method="{{ $claim->form_of_payment }}" 
                            data-date="{{ $claim->payout_date }}"
                            data-status="{{ $claim->disbursement?->claim_status ?? 'pending' }}"
                            style="border: none;"
                        >
                            <td class="no-border">
                                 @if ($claim->disbursement?->claim_status !== 'claimed')
                                    <input type="checkbox" class="claim-checkbox" name="selected_claims[]" value="{{ $claim->id }}">
                                @endif
                            </td>
                            <td style="border: none;">{{ $claim->client->full_name ?? '-' }}</td>
                            <td style="border: none;">{{ $claim->client->payee->full_name ?? '-' }}</td>
                            <td style="border: none;">
                                {{ $claim->client->payee && !$claim->client->payee->is_self_payee
                                    ? $claim->client->payee->contact_number
                                    : $claim->client->contact_number }}
                            </td>
                            <td style="border: none;">{{ $claim->client->municipality->name ?? '-' }}</td>
                            <td style="border: none;">₱{{ number_format($claim->amount_approved ?? 0, 2) }}</td>
                            <td style="border: none;">{{ $claim->disbursement->date_received_claimed ? \Carbon\Carbon::parse($claim->disbursement->date_received_claimed)->format('F d, Y') : '-' }}</td>
                            <td class="fw-semibold text-capitalize" style="border: none;">
                                @if($claim->disbursement?->claim_status === 'claimed')
                                    <span class="badge bg-success">Claimed</span>
                                @elseif($claim->disbursement?->claim_status === 'unclaimed')
                                    <span class="badge bg-warning text-dark">Unclaimed</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td style="border: none;">{{ ucfirst($claim->form_of_payment ?? '-') }}</td>
                            <td class="no-border">
                                @if(strtolower($claim->form_of_payment) === 'cheque')
                                    {{-- Change this line to get check number from claim instead of disbursement --}}
                                    {{ $claim->checkPayment->check_no ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="border: none;">{{ $claim->payout_date ? \Carbon\Carbon::parse($claim->payout_date)->format('F d, Y') : '-' }}</td>
                            <td class="text-center" style="border: none;">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($claim->disbursement?->claim_status !== 'claimed')
                                        <button class="btn p-1" style="border: none; background: none;" data-bs-toggle="modal" data-bs-target="#editDisbursementModal{{ $claim->id }}" title="Edit">
                                            <i class="bi bi-pencil-square text-success"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('clients.show', $claim->client_id) }}" class="btn p-1" style="border: none; background: none;" title="View Client">
                                        <i class="fas fa-eye text-success"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="mb-0 text-muted small">
                    Showing {{ $allClaims->count() }} results
                </p>

            </div>
            </div>
        </div>

        {{-- edit MODAL --}}
        @foreach($allClaims as $claim)
            @if ($claim->disbursement)
                <div class="modal fade" id="editDisbursementModal{{ $claim->id }}" tabindex="-1" aria-labelledby="editDisbursementModalLabel{{ $claim->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('disbursements.updateClaimStatus', $claim->disbursement->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                                <div class="modal-header border-0 pb-0" style="background: none;">
                                    <h5 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" style="font-size: 1.5rem;">Update Disbursement Info</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body row g-4 pt-3">
                                    <div class="col-md-6">
                                        <label for="date_received_{{ $claim->id }}" class="form-label fw-semibold">Date Received (Claimed)</label>
                                        <input type="date" id="date_received_{{ $claim->id }}" class="form-control rounded border border-success bg-light" name="date_received_claimed" value="{{ $claim->disbursement->date_received_claimed }}" style="box-shadow: none;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Total Amount Claimed</label>
                                        <p class="form-control rounded border border-success bg-light" style="box-shadow: none;">
                                            ₱{{ number_format($claim->amount_approved ?? 0, 2) }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="claim_status_{{ $claim->id }}" class="form-label fw-semibold">Claim Status</label>
                                        <select id="claim_status_{{ $claim->id }}" name="claim_status" class="form-select rounded border border-success bg-light" style="box-shadow: none;">
                                            <option value="pending" {{ $claim->disbursement->claim_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="claimed" {{ $claim->disbursement->claim_status === 'claimed' ? 'selected' : '' }}>Claimed</option>
                                            <option value="unclaimed" {{ $claim->disbursement->claim_status === 'unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2" style="background: none;">
                                    <button type="submit" class="btn btn-success custom-green-btn rounded-pill px-4">Save Changes</button>
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Batch Edit Modal -->
        <div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('disbursements.batchUpdate') }}">
                    @csrf
                    <input type="hidden" name="selected_ids" id="selectedIdsInput">

                    <div class="modal-content border-0 shadow-sm rounded p-4" style="background: #fff;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title w-100 text-center fw-bold text-success bg-success bg-opacity-10 rounded py-2 mb-0" id="batchEditModalLabel">
                                Batch Edit Disbursements
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row g-4 pt-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date Received (Claimed)</label>
                                <input type="date" name="date_received_claimed" class="form-control rounded border border-success bg-light">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Claim Status</label>
                                <select name="claim_status" id="batchStatusSelect" class="form-select rounded border border-success bg-light">
                                    <option value="">Do not change</option>
                                    <option value="claimed">Claimed</option>
                                    <option value="unclaimed">Unclaimed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success rounded-pill px-4">Update Selected</button>
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert border-0" style="background: #e9f7f1; color: #374151; font-weight: 500; border-radius: 0.7rem;">
            No approved claims found.
        </div>
    @endif

    {{-- Custom Alert Modal --}}
    <div id="customAlert" class="custom-alert">
      <div class="custom-alert-content">
        <p id="customAlertMessage"></p>
        <button id="customAlertClose">OK</button>
      </div>
    </div>
    
</main>
@endsection

@section('scripts')
<script>

    function showCustomAlert(message, type = 'warning') {
    const alertBox = document.getElementById('customAlert');
    const alertMessage = document.getElementById('customAlertMessage');
    const alertClose = document.getElementById('customAlertClose');

    alertMessage.textContent = message;

    // Remove previous types
    alertBox.classList.remove('success', 'error', 'warning');
    alertBox.classList.add(type); // add the new type class

    alertBox.style.display = 'flex';

    alertClose.onclick = () => {
        alertBox.style.display = 'none';
    };
}


    
    // warning
    @foreach($allClaims as $claim)
    @if ($claim->disbursement)
        const statusSelect{{ $claim->id }} = document.getElementById('claim_status_{{ $claim->id }}');
        const dateInput{{ $claim->id }} = document.getElementById('date_received_{{ $claim->id }}');
        const form{{ $claim->id }} = document.querySelector('#editDisbursementModal{{ $claim->id }} form');

        // Auto-set date when status is claimed
        statusSelect{{ $claim->id }}.addEventListener('change', () => {
            if (statusSelect{{ $claim->id }}.value === 'claimed' && !dateInput{{ $claim->id }}.value) {
                dateInput{{ $claim->id }}.valueAsDate = new Date();
            }
        });

        // Warn if date is set but status not claimed
        form{{ $claim->id }}.addEventListener('submit', (e) => {
            if (dateInput{{ $claim->id }}.value && statusSelect{{ $claim->id }}.value !== 'claimed') {
                e.preventDefault();
                showCustomAlert('You must set the claim status to "Claimed" if you enter a Date Received.');
            }
        });
    @endif
@endforeach


// Batch edit warning validation
const batchForm = document.querySelector('#batchEditModal form');
batchForm.addEventListener('submit', function(e) {
    const status = document.getElementById('batchStatusSelect').value;
    const dateInput = this.querySelector('input[name="date_received_claimed"]').value;

    if (dateInput && status !== 'claimed') {
        e.preventDefault();
        showCustomAlert('You must set the claim status to "claimed" if you enter a received date.');
    }
});



    document.getElementById('openBatchEditModalBtn').addEventListener('click', function () {
        const selectedIds = document.getElementById('selectedIdsInput').value.split(',');
        
        let firstClaimRow = document.querySelector(`.claim-checkbox[value="${selectedIds[0]}"]`).closest('tr');
        let currentStatus = firstClaimRow.getAttribute('data-status');
        
        const statusSelect = document.getElementById('batchStatusSelect'); // your modal's select element
        if (statusSelect) {
            statusSelect.value = currentStatus;
        }
    });


    document.addEventListener("DOMContentLoaded", function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.claim-checkbox');
        const batchEditBtn = document.getElementById('openBatchEditModalBtn');
        const selectedIdsInput = document.getElementById('selectedIdsInput');

        function updateSelectedIds() {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            selectedIdsInput.value = selectedIds.join(',');
            batchEditBtn.disabled = selectedIds.length === 0;
        }

        // Handle individual checkbox change
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedIds);
        });

        // Handle "select all" checkbox
        selectAllCheckbox.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedIds();
        });

        // Filter logic already exists; no need to change
    });

    document.getElementById('select-all').addEventListener('change', function () {
        let checkboxes = document.querySelectorAll('.claim-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.addEventListener("DOMContentLoaded", function () {
        @if (session('modal'))
            const modalId = "{{ session('modal') }}";
            const modalEl = document.getElementById(modalId);

            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            } else {
                console.warn(`Modal with ID '${modalId}' not found in the DOM.`);
            }
        @endif

        // Filtering logic
        const methodFilter = document.getElementById('methodFilter');
        const dateFilter = document.getElementById('dateFilter');
        const statusFilter = document.getElementById('statusFilter');

        const tableRows = document.querySelectorAll('#claimsTable tbody tr');

        function filterRows() {
            const method = methodFilter.value;
            const date = dateFilter.value;
            const status = statusFilter.value;

            tableRows.forEach(row => {
                const rowMethod = row.getAttribute('data-method') || '';
                const rowDate = row.getAttribute('data-date') || '';
                const rowStatus = row.getAttribute('data-status') || '';

                let show = true;
                if (method && rowMethod !== method) show = false;
                if (date && rowDate !== date) show = false;
                 if (status && rowStatus !== status) show = false;
                row.style.display = show ? '' : 'none';
            });
        }

        methodFilter?.addEventListener('change', filterRows);
        dateFilter?.addEventListener('change', filterRows);
        statusFilter?.addEventListener('change', filterRows);
    });

    // excelDownloadBtn
    document.getElementById('downloadExcelBtn').addEventListener('click', function (e) {
        e.preventDefault();

        const method = document.getElementById('methodFilter').value;
        const date = document.getElementById('dateFilter').value;
        const status = document.getElementById('statusFilter').value;

        const query = new URLSearchParams({
            method: method,
            date: date,
            status: status
        });

        // Redirect to the route with query string
        window.location.href = "{{ route('grouped-payouts.download.excel') }}?" + query.toString();
    });
    // PDFDownloadBtn
    document.getElementById('downloadPDFBtn').addEventListener('click', function (e) {
        e.preventDefault();

        const method = document.getElementById('methodFilter').value;
        const date = document.getElementById('dateFilter').value;
        const status = document.getElementById('statusFilter').value;

        const query = new URLSearchParams({
            method: method,
            date: date,
            status: status
        });

        // Redirect to the route with query string
        window.location.href = "{{ route('grouped-payouts.download.pdf') }}?" + query.toString();
    });
</script>
@endsection
