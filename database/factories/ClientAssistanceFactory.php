<?php

namespace Database\Factories;

use App\Models\ClientAssistance;
use App\Models\Client;
use App\Models\Payee;
use App\Models\AssistanceType;
use App\Models\AssistanceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientAssistanceFactory extends Factory
{
    protected $model = ClientAssistance::class;

    public function definition(): array
    {
        $assistanceType = AssistanceType::inRandomOrder()->first() ?? AssistanceType::factory()->create();

        $category = AssistanceCategory::where('assistance_type_id', $assistanceType->id)->inRandomOrder()->first()
            ?? AssistanceCategory::factory()->create([
                'assistance_type_id' => $assistanceType->id,
            ]);

        $client = Client::inRandomOrder()->first() ?? Client::factory()->create();

        return [
            // CHANGED: use factories for foreign keys
            'client_id' => Client::factory(),
            'assistance_type_id' => AssistanceType::factory(),
            'assistance_category_id' => $category->id,
            'payee_id' => null,
            'date_received_request' => $this->faker->date(),
        ];
    }
}
