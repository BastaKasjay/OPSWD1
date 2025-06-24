<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDisbursement extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_disbursement_id';

    protected $fillable = [
        'cash_payment_id',
        'client_id',
        'amount',
        'confirmation_date',
        'date_received_claimed',
        'date_released',
        'total_amount_claimed',
    ];
}