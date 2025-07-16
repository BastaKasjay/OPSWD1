<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User; // ADDED: import User model
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        //$user = User::factory()->create(); // CHANGED: create a user for assessed_by

        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'sex' => $this->faker->randomElement(['male', 'female']),
            'age' => $this->faker->numberBetween(18, 80),
            'address' => $this->faker->address(),
            'contact_number' => $this->faker->phoneNumber(),
            'municipality_id' => 1, // You may want to ensure this exists or use a factory
            //'assessed_by' => $user->id, // CHANGED: use created user's ID
            'valid_id' => $this->faker->boolean(),
        ];
    }
}
