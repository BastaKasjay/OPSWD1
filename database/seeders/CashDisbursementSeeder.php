<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashDisbursement;

class CashDisbursementSeeder extends Seeder
{
    public function run(): void
    {
        CashDisbursement::factory()->count(10)->create();
    }
}