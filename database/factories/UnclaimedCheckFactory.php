<?php

namespace Database\Factories;

use App\Models\UnclaimedCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnclaimedCheckFactory extends Factory
{
    protected $model = UnclaimedCheck::class;

    public function definition(): array
    {
        return [
            'check_payment_id' => 1, // Adjust as needed
            'client_id' => 1, // Adjust as needed
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
            'check_number' => $this->faker->unique()->numerify('CHK#####'),
            'date_prepared' => $this->faker->date(),
            'elapsed_time' => $this->faker->numberBetween(1, 30),
        ];
    }
}