<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed positions and players
        $this->call([
            PositionSeeder::class,
            PlayerSeeder::class,
            StrongStatsSeeder::class,   // Estatísticas muito boas, muitos jogos (141 jogadores, até 40 jogos)
        ]);
    }
}
