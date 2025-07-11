<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'claim_id',
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

    public function claim()
    {
        return $this->belongsTo(\App\Models\Claim::class, 'claim_id');
    }
    

}