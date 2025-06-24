<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnclaimedCheck;

class UnclaimedCheckSeeder extends Seeder
{
    public function run(): void
    {
        UnclaimedCheck::factory()->count(10)->create();
    }
}