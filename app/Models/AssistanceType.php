<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistanceType extends Model
{
    use HasFactory;

    protected $table = 'assistance_types'; // or 'assistance_types' if plural

    protected $fillable = ['assistance_type'];

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function categories()
    {
        return $this->hasMany(AssistanceCategory::class);
    }
}
