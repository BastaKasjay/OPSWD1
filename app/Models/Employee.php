<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'office',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function getFullNameAttribute()
{
    return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
}

}
