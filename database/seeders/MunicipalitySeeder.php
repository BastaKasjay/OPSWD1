<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Municipality::create(['Atok' => 'Atok']);
        Municipality::create(['Bakun' => 'Bakun']);
        Municipality::create(['Bokod' => 'Bokod']);
        Municipality::create(['Bugias' => 'Bugias']);
        Municipality::create(['Itogon' => 'Itogon']);
        Municipality::create(['La Trinidad' => 'La Trinidad']);
        Municipality::create(['Kabayan' => 'Kabayan']);
        Municipality::create(['Kapangan' => 'Kapangan']);
        Municipality::create(['Mankayan' => 'Mankayan']);
        Municipality::create(['Sablan' => 'Sablan']);
        Municipality::create(['Tuba' => 'Tuba']);
        Municipality::create(['Tublay' => 'Tublay']);
        Municipality::create(['Kibungan' => 'Kibungan']);

    }
}
