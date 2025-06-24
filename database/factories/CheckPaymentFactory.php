<?php

namespace Database\Factories;

use App\Models\CheckPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckPaymentFactory extends Factory
{
    protected $model = CheckPayment::class;

    public function definition(): array
    {
        return [
            // CHANGED: 'approved_claims_id' to 'claim_id' to match migration
            'claim_id' => 1, // was 'approved_claims_id'
            'client_id' => 1, // Adjust as needed
            'date_prepared' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
            'check_no' => $this->faker->unique()->numerify('CHK#####'),
            'date_claimed' => $this->faker->optional()->date(),
            'status' => 'pending due to payee change',
        ];
    }
}