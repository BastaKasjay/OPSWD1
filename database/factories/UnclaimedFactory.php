<?php

namespace Database\Factories;

use App\Models\Unclaimed;
use App\Models\Client;
use App\Models\CashPayment;
use App\Models\CheckPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnclaimedFactory extends Factory
{
    protected $model = Unclaimed::class;

    public function definition(): array
    {
        // Randomly decide if it's cash or check (only one will be filled)
        $isCash = $this->faker->boolean;

        return [
            'client_id' => Client::factory(), // Create or associate a client
            'cash_payment_id' => $isCash ? CashPayment::factory() : null,
            'check_payment_id' => $isCash ? null : CheckPayment::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
            'check_number' => $isCash ? null : $this->faker->unique()->numerify('CHK#####'),
            'date_prepared' => $this->faker->date(),
            'elapsed_time' => $this->faker->numberBetween(1, 30),
        ];
    }
}
