<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    use HasFactory;

    protected $table = 'payee'; // since the table is not plural

    protected $primaryKey = 'payee_id';

    protected $fillable = [
        'client_id',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'relationship',
        'proof_of_relationship',
        'updated_to_new_payee',
        'previous_payee_name',
    ];

    // Define relationship to Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
