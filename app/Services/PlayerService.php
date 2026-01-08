<?php

namespace App\Services;

use App\Models\Player;
use App\Repositories\PlayerRepository;

class PlayerService
{
    public function __construct(private PlayerRepository $playerRepository) {}

    public function create(array $data): Player
    {
        return $this->playerRepository->create($data);
    }

    public function update(Player $player, array $data): Player
    {
        return $this->playerRepository->update($player, $data);
    }

    public function delete(Player $player): bool
    {
        return $this->playerRepository->delete($player);
    }

    public function getActive()
    {
        return $this->playerRepository->getActive();
    }

    public function getAll()
    {
        return $this->playerRepository->getAll();
    }

    public function getById(int $id): ?Player
    {
        return $this->playerRepository->getById($id);
    }

    public function getByPosition(int $positionId)
    {
        return $this->playerRepository->getByPosition($positionId);
    }

    public function getStats(Player $player): array
    {
        return $this->playerRepository->getPlayerStats($player);
    }

    public function formatForFrontend($players)
    {
        return $players->map(function (Player $player) {
            return [
                'id' => $player->id,
                'name' => $player->name,
                'email' => $player->email,
                'position_id' => $player->position_id,
                'position' => $player->position->name,
                'position_code' => $player->position->code,
                'active' => $player->active,
                'stats' => $this->getStats($player),
            ];
        });
    }
}
