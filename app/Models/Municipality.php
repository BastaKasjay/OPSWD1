<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
        public function clients()
    {
        return $this->hasMany(Client::class);
    }


}


