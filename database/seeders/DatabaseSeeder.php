<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MunicipalitySeeder::class,
            VulnerabilitySectorSeeder::class,
            AssistanceSeeder::class, // ✅ Add this line
        ]);
    }
}
