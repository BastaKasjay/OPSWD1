<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = ['assistance_type_id', 'requirement'];

    public function assistanceType()
    {
        return $this->belongsTo(AssistanceType::class);
    }
}
