<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unclaimed;

class UnclaimedSeeder extends Seeder
{
    public function run(): void
    {
        Unclaimed::factory()->count(10)->create();
    }
}
