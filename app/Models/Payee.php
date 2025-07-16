<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    use HasFactory;

    protected $table = 'payees';

    protected $fillable = [
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'relationship',
        'proof_of_relationship',
        'contact_number',
        'updated_to_new_payee',
        'previous_payee_id',
        'valid_id',
        'is_self_payee', 
    ];

    // Corrected relationship
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    protected static function booted()
    {
        static::saving(function ($payee) {
            if (!$payee->is_self_payee) {
                $payee->full_name = trim("{$payee->first_name} {$payee->middle_name} {$payee->last_name}");
            }
        });
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }


}
