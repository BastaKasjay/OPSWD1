<?php

namespace Database\Factories;

use App\Models\AssistanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssistanceTypeFactory extends Factory
{
    protected $model = AssistanceType::class;

    public function definition(): array
    {
        return [
            'type_name' => $this->faker->unique()->word(),
        ];
    }
}
