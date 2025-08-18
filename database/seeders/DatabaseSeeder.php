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
            AssistanceSeeder::class, 
            // CheckPaymentSeeder::class,
            // UnclaimedSeeder::class,
            // CashPaymentSeeder::class,
            // DisbursementSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
