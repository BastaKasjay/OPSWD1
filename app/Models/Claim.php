<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_assistance_id',
        'status',
        'reason_of_disapprovement',
        'amount_approved',
        'date_cafoa_prepared',
        'date_pgo_received',
        'date_pto_received',
        'form_of_payment',
        'confirmation',
    ];

    public function clientAssistance()
    {
        return $this->belongsTo(\App\Models\ClientAssistance::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    public function cashPayment()
    {
        return $this->hasOne(\App\Models\CashPayment::class, 'claim_id');
    }



}
