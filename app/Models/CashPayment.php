<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'cash_payment_id';

    protected $fillable = [
        'approved_claims_id',
        'client_id',
        'date_prepared',
        'confirmed_people',
        'amount_confirmed',
        'total_amount_withdrawn',
        'date_of_payout',
    ];

    protected $casts = [
        'confirmed_people' => 'array',
    ];
}