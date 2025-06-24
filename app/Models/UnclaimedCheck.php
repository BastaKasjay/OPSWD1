<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnclaimedCheck extends Model
{
    use HasFactory;

    protected $primaryKey = 'unclaimed_check_id';

    protected $fillable = [
        'check_payment_id',
        'client_id',
        'amount',
        'check_number',
        'date_prepared',
        'elapsed_time',
    ];
}