<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckPayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'check_payment_id';

    protected $fillable = [
        'approved_claims_id',
        'client_id',
        'date_prepared',
        'amount',
        'check_no',
        'date_claimed',
        'status',
    ];
}