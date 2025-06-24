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
}
