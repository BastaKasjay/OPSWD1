<?php

namespace Database\Factories;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        $client = \App\Models\Client::factory()->create();
        $clientAssistance = \App\Models\ClientAssistance::factory()->create();

        return [
            'client_id' => $client->id,
            'client_assistance_id' => $clientAssistance->id, // You may want to ensure this exists or use a factory
            'status' => $this->faker->randomElement(['approved', 'disapproved']),
            'reason_of_disapprovement' => $this->faker->optional()->sentence(),
            'amount_approved' => $this->faker->randomFloat(2, 1000, 10000),
            'date_cafoa_prepared' => $this->faker->optional()->date(),
            'date_pgo_received' => $this->faker->optional()->date(),
            'form_of_payment' => $this->faker->optional()->randomElement(['cash', 'cheque']),
            'payout_date' => $this->faker->optional()->date(),
        ];
    }
}
