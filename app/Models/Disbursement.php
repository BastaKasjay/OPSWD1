<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use HasFactory;


    protected $fillable = [
        'claim_id',
        'client_id',
        'cash_payment_id',
        'check_payment_id',
        'form_of_payment',
        'amount',
        'confirmation_date',
        'date_received_claimed',
        'date_released',
        'total_amount_claimed',
        'claim_status',
    ];

    // Claim.php
    public function claim()
    {
        return $this->belongsTo(\App\Models\Claim::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

}