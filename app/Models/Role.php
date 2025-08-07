<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Use the actual column name in the DB
    protected $fillable = ['rolename'];

    // In Role.php
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles')->withTimestamps();
    }


    // Optional: so you can still use $role->name in code
    public function getNameAttribute()
    {
        return $this->rolename;
    }
}
