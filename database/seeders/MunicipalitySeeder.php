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

        $municipalities = [
            'Atok', 'Bakun', 'Bokod', 'Buguias', 'Itogon',
            'La Trinidad', 'Kabayan', 'Kapangan', 'Kibungan', 'Mankayan',
            'Sablan', 'Tuba', 'Tublay'
        ];

        foreach ($municipalities as $muni) {
            Municipality::firstOrCreate(['name' => $muni]);
        }
    }
    
}
