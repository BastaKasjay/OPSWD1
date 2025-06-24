<?php

namespace Database\Factories;

use App\Models\ClientAssistance;
use App\Models\Client;
use App\Models\AssistanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientAssistanceFactory extends Factory
{
    protected $model = ClientAssistance::class;

    public function definition(): array
    {
        return [
            // CHANGED: use factories for foreign keys
            'client_id' => Client::factory(),
            'assistance_type_id' => AssistanceType::factory(),
            'payee_id' => null,
            'date_received_request' => $this->faker->date(),
        ];
    }
}
