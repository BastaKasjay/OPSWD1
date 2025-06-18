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
        'address',
        'contact_number',
        'valid_id',
        'assessed_by',
        'municipality_id',
    ];

    // Relationship to Municipality
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    // Relationship to VulnerabilitySector
    public function vulnerabilitySectors()
    {
        return $this->belongsToMany(VulnerabilitySector::class, 'client_vulnerability_sector');
    }

    // Relationship to User (Assessor)
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

}