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
        'client_assistance_id',
        'cash_payment_id',
        'check_payment_id',
        'form_of_payment',
        'check_no',
        'amount',
        'payout_date',
        'date_received_claimed',
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
    public function municipality()
    {
        return $this->belongsTo(\App\Models\Municipality::class, 'municipality_id');
    }
    public function clientAssistance()
    {
        return $this->belongsTo(\App\Models\ClientAssistance::class, 'client_assistance_id');
    }
    public function checkPayment()
    {
        return $this->hasOne(CheckPayment::class);
    }



}