<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistanceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['assistance_type_id', 'category'];

    public function assistanceType()
    {
        return $this->belongsTo(AssistanceType::class);
    }
}
