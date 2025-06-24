<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckPayment;
use App\Models\Claim; // ADDED: import Claim model

class CheckPaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Create a claim and use its ID
        $claim = Claim::factory()->create();
        CheckPayment::factory()->count(10)->create([
            'claim_id' => $claim->id,
        ]);
    }
}