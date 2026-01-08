<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        // Obter posições
        $gk = Position::where('code', 'GK')->first();
        $def = Position::where('code', 'DEF')->first();
        $mid = Position::where('code', 'MID')->first();
        $fwd = Position::where('code', 'FWD')->first();

        // Jogadores de exemplo (mais equilibrado com mais GK's)
        $players = [
            // Guarda-redes (4)
            ['name' => 'João Silva', 'email' => 'joao@example.com', 'position_id' => $gk->id],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro@example.com', 'position_id' => $gk->id],
            ['name' => 'Hélder Rocha', 'email' => 'helder@example.com', 'position_id' => $gk->id],
            ['name' => 'Tiago Lopes', 'email' => 'tiago.lopes@example.com', 'position_id' => $gk->id],

            // Defesas (5)
            ['name' => 'Carlos Santos', 'email' => 'carlos@example.com', 'position_id' => $def->id],
            ['name' => 'Miguel Costa', 'email' => 'miguel@example.com', 'position_id' => $def->id],
            ['name' => 'André Martins', 'email' => 'andre@example.com', 'position_id' => $def->id],
            ['name' => 'Bruno Dias', 'email' => 'bruno@example.com', 'position_id' => $def->id],
            ['name' => 'Filipe Santos', 'email' => 'filipe@example.com', 'position_id' => $def->id],

            // Médios (5)
            ['name' => 'Gonçalo Alves', 'email' => 'goncalo@example.com', 'position_id' => $mid->id],
            ['name' => 'Ricardo Ferreira', 'email' => 'ricardo@example.com', 'position_id' => $mid->id],
            ['name' => 'Nuno Rocha', 'email' => 'nuno@example.com', 'position_id' => $mid->id],
            ['name' => 'Paulo Mendes', 'email' => 'paulo@example.com', 'position_id' => $mid->id],
            ['name' => 'Duarte Silva', 'email' => 'duarte@example.com', 'position_id' => $mid->id],

            // Avançados (4)
            ['name' => 'Tiago Moreira', 'email' => 'tiago@example.com', 'position_id' => $fwd->id],
            ['name' => 'Luís Gomes', 'email' => 'luis@example.com', 'position_id' => $fwd->id],
            ['name' => 'Rui Pereira', 'email' => 'rui@example.com', 'position_id' => $fwd->id],
            ['name' => 'Jorge Baptista', 'email' => 'jorge@example.com', 'position_id' => $fwd->id],
            ['name' => 'Paulo Coelho', 'email' => 'paulo.coelho@example.com', 'position_id' => $fwd->id],
        ];

        foreach ($players as $player) {
            Player::firstOrCreate(
                ['email' => $player['email']],
                $player
            );
        }
    }
}
