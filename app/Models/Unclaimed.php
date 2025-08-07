<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unclaimed extends Model
{
    use HasFactory;

    protected $table = 'unclaimed';

    protected $fillable = [
        'client_id',
        'cash_payment_id',
        'check_payment_id',
        'amount',
        'check_number',
        'date_prepared',
        'elapsed_time',
    ];

    // Relationships

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function cashPayment()
    {
        return $this->belongsTo(CashPayment::class);
    }

    public function checkPayment()
    {
        return $this->belongsTo(CheckPayment::class);
    }
}
