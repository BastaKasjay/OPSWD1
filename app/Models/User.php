<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'employee_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Automatically hash password unless already hashed
    public function setPasswordAttribute($value)
    {
        if (substr($value, 0, 4) !== '$2y$') {
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles')->withTimestamps();
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('rolename', $roleName)->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }
}
