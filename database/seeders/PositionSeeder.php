<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'code' => 'GK',
                'name' => 'Guarda-redes',
                'max_per_team' => 1,
                'sort_order' => 1,
            ],
            [
                'code' => 'DEF',
                'name' => 'Defesa',
                'max_per_team' => 4,
                'sort_order' => 2,
            ],
            [
                'code' => 'MID',
                'name' => 'Médio',
                'max_per_team' => 4,
                'sort_order' => 3,
            ],
            [
                'code' => 'FWD',
                'name' => 'Avançado',
                'max_per_team' => 3,
                'sort_order' => 4,
            ],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['code' => $position['code']],
                $position
            );
        }
    }
}
