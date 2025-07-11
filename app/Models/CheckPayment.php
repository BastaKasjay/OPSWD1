<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'claim_id',
        'client_id',
        'date_prepared',
        'amount',
        'check_no',
        'date_claimed',
        'status',
    ];

    public function claim()
    {
        return $this->belongsTo(\App\Models\Claim::class, 'claim_id');
    }
}