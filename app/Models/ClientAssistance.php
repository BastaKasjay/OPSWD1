<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAssistance extends Model
{
    use HasFactory;

    protected $table = 'client_assistance';

    protected $fillable = [
        'client_id',
        'created_by',
        'assistance_type_id',
        'assistance_category_id',
        'other_category_name',
        'medical_case',
        'payee_id',
        'date_received_request',
    ];

    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assistanceType()
    {
        return $this->belongsTo(AssistanceType::class, 'assistance_type_id');
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class, 'payee_id');
    }

    public function claim()
    {
        return $this->hasOne(\App\Models\Claim::class);
    }

    public function assistanceCategory()
    {
        return $this->belongsTo(\App\Models\AssistanceCategory::class);
    }

    public function createdByEmployee()
{
    return $this->belongsTo(Employee::class, 'created_by');
}

    


}
