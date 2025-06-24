<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashPayment;

class CashPaymentSeeder extends Seeder
{
    public function run(): void
    {
        CashPayment::factory()->count(10)->create();
    }
}