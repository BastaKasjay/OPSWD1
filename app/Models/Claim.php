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

    public function isClaimed()
    {
        if ($this->form_of_payment === 'cash') {
            return $this->cashDisbursement()->exists();
        }

        if ($this->form_of_payment === 'cheque') {
            return $this->checkDisbursement()->exists();
        }

        return false;
    }


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

    public function disbursement()
    {
        return $this->hasOne(\App\Models\Disbursement::class);
    }

    public function cashDisbursement()
    {
        return $this->hasOne(\App\Models\Disbursement::class)->where('form_of_payment', 'cash');
    }

    public function checkDisbursement()
    {
        return $this->hasOne(\App\Models\Disbursement::class)->where('form_of_payment', 'cheque');
    }

    public function checkPayment()
    {
        return $this->hasOne(\App\Models\CheckPayment::class, 'claim_id');
    }



}
