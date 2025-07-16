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
        'assistance_type_id',
        'assistance_category_id',
        'assistance_type_id',
        'assistance_category_id',
        'payee_id',
        'date_received_request',
    ];

    // Relationships
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
    


}
