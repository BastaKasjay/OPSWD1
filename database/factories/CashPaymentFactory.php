<?php

namespace Database\Factories;

use App\Models\CashPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashPaymentFactory extends Factory
{
    protected $model = CashPayment::class;

    public function definition(): array
    {
        return [
            'claim_id' => 1, // CHANGED: 'approved_claims_id' to 'claim_id' to match migration
            'client_id' => 1, // Adjust as needed
            'date_prepared' => $this->faker->date(),
            'confirmed_people' => json_encode([$this->faker->name, $this->faker->name]),
            'amount_confirmed' => $this->faker->randomFloat(2, 1000, 10000),
            'total_amount_withdrawn' => $this->faker->randomFloat(2, 1000, 10000),
            'date_of_payout' => $this->faker->date(),
        ];
    }
}