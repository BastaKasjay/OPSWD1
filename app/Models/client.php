<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'relationship',
        'sex',
        'age',
        'is_4ps',
        'is_pwd',
        'address',
        'contact_number',
        'valid_id',
        'assessed_by',
    ];
}