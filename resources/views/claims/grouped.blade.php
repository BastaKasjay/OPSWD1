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
        @foreach($payments as $paymentMethod => $claims)
            <div class="mb-5">
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($claims as $claim)
                                
                                <tr>
                                    <td>{{ $claim->client->full_name ?? '-' }}</td>
                                    <td>{{ $claim->client->payee->full_name ?? '-' }}</td>
                                    <td>
                                        {{
                                            $claim->client->payee && !$claim->client->payee->is_self_payee
                                                ? $claim->client->payee->contact_number
                                                : $claim->client->contact_number
                                        }}
                                    </td>

                                    <td>{{ $claim->client->municipality->name ?? '-' }}</td>
                                    <td>₱{{ number_format($claim->amount_approved ?? 0, 2) }}</td>

                                    <td>
                                        <a href="{{ route('clients.show', $claim->client_id) }}" class="btn btn-sm btn-outline-primary me-1">View</a>
                                        <a href="{{ route('clients.show', $claim->client_id) }}" class="text-success" title="View Client">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @empty
        <div class="alert alert-warning">No approved claims found with confirmation + payment method.</div>
    @endforelse
</main>
@endsection
