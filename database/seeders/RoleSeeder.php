<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // make sure you have a Role model

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'officer',];

        foreach ($roles as $role) {
            Role::firstOrCreate(['rolename' => $role]);
        }
    }
}
