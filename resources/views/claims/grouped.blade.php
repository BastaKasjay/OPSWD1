@extends('layouts.app')

@section('content')
<main class="p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('clients.assistance') }}" class="text-secondary me-2">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
    <h1 class="fs-3 fw-bold mb-4">Grouped Payouts</h1>
    

    @forelse($grouped as $confirmationDate => $payments)

    <!-- total of payout claimed per quarter -->
    @php
        $allClaims = collect($grouped)->flatten(2); 
        $grandTotal = $allClaims
            ->filter(fn($claim) => $claim->disbursement?->claim_status === 'claimed')
            ->sum(fn($claim) => $claim->disbursement->total_amount_claimed ?? 0);
    @endphp

    @if ($allClaims->isNotEmpty())
        <div class="mt-4 text-end">
            <h5 class="fw-bold text-primary">
                Grand Total Amount Claimed this Quarter: ₱{{ number_format($grandTotal, 2) }}
            </h5>
        </div>
    @endif

        @foreach($payments as $paymentMethod => $claims)
            <div class="mb-5 position-relative">
                <h4 class="text-primary">
                    Date: {{ \Carbon\Carbon::parse($confirmationDate)->format('F d, Y') }} — Method: {{ ucfirst($paymentMethod) }}
                </h4>

                <div class="table-responsive bg-white p-3 shadow rounded">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Client Name</th>
                                <th>Representative</th>
                                <th>Contact</th>
                                <th>Municipality</th>
                                <th>Amount Approved</th>
                                <th>Date Received</th>
                                <th>Date Released</th>
                                <th>Claim Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claims as $claim)
                                <tr>
                                    <td>{{ $claim->client->full_name ?? '-' }}</td>
                                    <td>{{ $claim->client->payee->full_name ?? '-' }}</td>
                                    <td>
                                        {{ $claim->client->payee && !$claim->client->payee->is_self_payee
                                            ? $claim->client->payee->contact_number
                                            : $claim->client->contact_number }}
                                    </td>
                                    <td>{{ $claim->client->municipality->name ?? '-' }}</td>
                                    <td>₱{{ number_format($claim->amount_approved ?? 0, 2) }}</td>
                                    <td>{{ $claim->disbursement->date_received_claimed ?? '-' }}</td>
                                    <td>{{ $claim->disbursement->date_released ?? '-' }}</td>
                                    <td class="fw-semibold text-capitalize">
                                        @if($claim->disbursement?->claim_status === 'claimed')
                                            <span class="badge bg-success">Claimed</span>
                                        @elseif($claim->disbursement?->claim_status === 'unclaimed')
                                            <span class="badge bg-warning text-dark">Unclaimed</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <button class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editDisbursementModal{{ $claim->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <a href="{{ route('clients.show', $claim->client_id) }}" class="text-success" title="View Client">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MODALS --}}
                @foreach($claims as $claim)
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
                                                <label for="claim_status_{{ $claim->id }}" class="form-label">Claim Status</label>
                                                <select id="claim_status_{{ $claim->id }}" name="claim_status" class="form-select">
                                                    <option value="pending" {{ $claim->disbursement->claim_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="claimed" {{ $claim->disbursement->claim_status === 'claimed' ? 'selected' : '' }}>Claimed</option>
                                                    <option value="unclaimed" {{ $claim->disbursement->claim_status === 'unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                                                </select>
                                            </div>

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
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>
        @endforeach
    @empty
        <div class="alert alert-warning">No approved claims found.</div>
    @endforelse
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
    });
</script>
@endsection
