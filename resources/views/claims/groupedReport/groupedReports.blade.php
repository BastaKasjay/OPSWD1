<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
                        <th class="fw-semibold text-success" style="background: none; border: none;">Date Released</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Claim Status</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Payment Method</th>
                        <th class="fw-semibold text-success" style="background: none; border: none;">Payout Date</th>
                        
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
                            <td>
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
                            <td style="border: none;">{{ $claim->payout_date ? \Carbon\Carbon::parse($claim->payout_date)->format('F d, Y') : '-' }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>


</body>
</html>