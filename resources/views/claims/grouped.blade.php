@extends('layouts.app')

@section('content')
<main class="p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="fs-3 fw-bold mb-4 text-dark">Grouped Payouts</h1>

    @php
        $allClaims = collect($grouped)->flatten(2);
        $grandTotal = $allClaims
            ->filter(fn($claim) => $claim->disbursement?->claim_status === 'claimed')
            ->sum(fn($claim) => $claim->disbursement->total_amount_claimed ?? 0);

        // Get unique payment methods and confirmation dates for filters
        $methods = $allClaims->pluck('form_of_payment')->unique()->filter()->values();
        $dates = $allClaims->pluck('confirmation')->unique()->filter()->sort()->values();
    @endphp

    @if ($allClaims->isNotEmpty())
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter by Payment Method:</label>
                <select id="methodFilter" class="form-select">
                    <option value="">All Methods</option>
                    @foreach($methods as $method)
                        <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Filter by Confirmation Date:</label>
                <select id="dateFilter" class="form-select">
                    <option value="">All Dates</option>
                    @foreach($dates as $date)
                        <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive bg-white p-4 rounded-lg shadow-md">
            <table class="table table-hover" id="claimsTable" style="width:100%; background: transparent; border: none;">
                <thead class="bg-success bg-opacity-10" style="border: none;">
                    <tr style="border: none;">
                        <th class="fw-semibold text-success" style="background: none; border: none;">Client Name</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Representative</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Contact</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Municipality</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Amount Approved</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Date Received</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Date Released</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Claim Status</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Payment Method</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Confirmation Date</th>
                        <th class="fw-semibold text-success text-center" style="background: none; width: 70px; border: none;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allClaims->sortBy(fn($c) => $c->client->municipality->name ?? '') as $claim)
                        <tr 
                            data-method="{{ $claim->form_of_payment }}" 
                            data-date="{{ $claim->confirmation }}"
                            style="border: none;"
                        >
                            <td style="border: none;">{{ $claim->client->full_name ?? '-' }}</td>
                            <td style="border: none;">{{ $claim->client->payee->full_name ?? '-' }}</td>
                            <td style="border: none;">
                                {{ $claim->client->payee && !$claim->client->payee->is_self_payee
                                    ? $claim->client->payee->contact_number
                                    : $claim->client->contact_number }}
                            </td>
                            <td style="border: none;">{{ $claim->client->municipality->name ?? '-' }}</td>
                            <td style="border: none;">₱{{ number_format($claim->amount_approved ?? 0, 2) }}</td>
                            <td style="border: none;">{{ $claim->disbursement->date_received_claimed ?? '-' }}</td>
                            <td style="border: none;">{{ $claim->disbursement->date_released ?? '-' }}</td>
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
                            <td style="border: none;">{{ $claim->confirmation ? \Carbon\Carbon::parse($claim->confirmation)->format('F d, Y') : '-' }}</td>
                            <td class="text-center" style="border: none;">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn p-1" style="border: none; background: none;" data-bs-toggle="modal" data-bs-target="#editDisbursementModal{{ $claim->id }}" title="Edit">
                                        <i class="bi bi-pencil-square text-success"></i>
                                    </button>
                                    <a href="{{ route('clients.show', $claim->client_id) }}" class="btn p-1" style="border: none; background: none;" title="View Client">
                                        <i class="fas fa-eye text-success"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MODALS --}}
        @foreach($allClaims as $claim)
            @if ($claim->disbursement)
                <div class="modal fade" id="editDisbursementModal{{ $claim->id }}" tabindex="-1" aria-labelledby="editDisbursementModalLabel{{ $claim->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('disbursements.updateClaimStatus', $claim->disbursement->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="editDisbursementModalLabel{{ $claim->id }}">Update Disbursement Info</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="date_received_{{ $claim->id }}" class="form-label">Date Received (Claimed)</label>
                                        <input type="date" id="date_received_{{ $claim->id }}" class="form-control" name="date_received_claimed" value="{{ $claim->disbursement->date_received_claimed }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_released_{{ $claim->id }}" class="form-label">Date Released</label>
                                        <input type="date" id="date_released_{{ $claim->id }}" class="form-control" name="date_released" value="{{ $claim->disbursement->date_released }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount_claimed_{{ $claim->id }}" class="form-label">Total Amount Claimed</label>
                                        <input type="number" step="0.01" id="amount_claimed_{{ $claim->id }}" class="form-control" name="total_amount_claimed" value="{{ $claim->disbursement->total_amount_claimed }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="claim_status_{{ $claim->id }}" class="form-label">Claim Status</label>
                                        <select id="claim_status_{{ $claim->id }}" name="claim_status" class="form-select">
                                            <option value="pending" {{ $claim->disbursement->claim_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="claimed" {{ $claim->disbursement->claim_status === 'claimed' ? 'selected' : '' }}>Claimed</option>
                                            <option value="unclaimed" {{ $claim->disbursement->claim_status === 'unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach

    @else
        <div class="alert border-0" style="background: #e9f7f1; color: #374151; font-weight: 500; border-radius: 0.7rem;">
            No approved claims found.
        </div>
    @endif
</main>
@endsection

@section('scripts')
<script>
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
        const tableRows = document.querySelectorAll('#claimsTable tbody tr');

        function filterRows() {
            const method = methodFilter.value;
            const date = dateFilter.value;
            tableRows.forEach(row => {
                const rowMethod = row.getAttribute('data-method') || '';
                const rowDate = row.getAttribute('data-date') || '';
                let show = true;
                if (method && rowMethod !== method) show = false;
                if (date && rowDate !== date) show = false;
                row.style.display = show ? '' : 'none';
            });
        }

        methodFilter?.addEventListener('change', filterRows);
        dateFilter?.addEventListener('change', filterRows);
    });
</script>
@endsection
