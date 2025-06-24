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
        'sex',
        'age',
        'address',
        'contact_number',

        'municipality_id',
        'assistance_type_id',
        'assistance_category_id',
        'representative_first_name',
        'representative_middle_name',
        'representative_last_name',
        'representative_contact_number',

        'assessed_by'
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function vulnerabilitySectors()
    {
        return $this->belongsToMany(VulnerabilitySector::class, 'client_vulnerability_sector');
    }

    public function assistanceType()
    {
        return $this->belongsTo(AssistanceType::class);
    }

    public function assistanceCategory()
    {
        return $this->belongsTo(\App\Models\AssistanceCategory::class);
    }
}
