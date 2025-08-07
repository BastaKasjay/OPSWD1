<?php

namespace App\Models;

use App\Models\Claim;
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
        'birthday',
        'municipality_id',
        'valid_id',
        'assistance_type_id',
        'assistance_category_id',

        'assessed_by'
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function vulnerabilitySectors()
    {
        return $this->belongsToMany(VulnerabilitySector::class, 'client_vulnerability_sector')
                ->withTimestamps();
    }

    public function assistanceType()
    {
        return $this->belongsTo(AssistanceType::class);
    }

    public function assistanceCategory()
    {
        return $this->belongsTo(\App\Models\AssistanceCategory::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
    public function assistances()
    {
        return $this->hasMany(ClientAssistance::class);
    }
    public function payee()
    {
        return $this->hasOne(Payee::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }


    }
