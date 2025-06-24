<?php

namespace Database\Factories;

use App\Models\CashDisbursement;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashDisbursementFactory extends Factory
{
    protected $model = CashDisbursement::class;

    public function definition(): array
    {
        // Only use real clients (manually added via the site)
        $client = Client::inRandomOrder()->first();

        return [
            'cash_payment_id' => 1, // Adjust as needed
            'client_id' => $client ? $client->id : null, // Use a real client or null if none exist
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
            'confirmation_date' => $this->faker->date(),
            'date_received_claimed' => $this->faker->date(),
            'date_released' => $this->faker->date(),
            'total_amount_claimed' => $this->faker->randomFloat(2, 1000, 10000),
        ];
    }
}