<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Player;
use App\Models\Position;
use App\Services\PlayerService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlayerController extends Controller
{
    public function __construct(private PlayerService $playerService) {}

    public function index(): Response
    {
        $players = $this->playerService->getAll();
        $formattedPlayers = $this->playerService->formatForFrontend($players);

        return Inertia::render('Players/Index', [
            'players' => $formattedPlayers,
        ]);
    }

    public function create(): Response
    {
        $positions = Position::orderBy('sort_order')->get();

        return Inertia::render('Players/Form', [
            'player' => null,
            'positions' => $positions,
        ]);
    }

    public function store(StorePlayerRequest $request)
    {
        $player = $this->playerService->create($request->validated());

        return redirect()->route('players.index')->with('success', 'Jogador criado com sucesso');
    }

    public function edit(Player $player): Response
    {
        $positions = Position::orderBy('sort_order')->get();

        return Inertia::render('Players/Form', [
            'player' => [
                'id' => $player->id,
                'name' => $player->name,
                'email' => $player->email,
                'position_id' => $player->position_id,
                'active' => $player->active,
            ],
            'positions' => $positions,
        ]);
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $this->playerService->update($player, $request->validated());

        return redirect()->route('players.index')->with('success', 'Jogador atualizado com sucesso');
    }

    public function destroy(Player $player)
    {
        $this->playerService->delete($player);

        return redirect()->route('players.index')->with('success', 'Jogador eliminado com sucesso');
    }

    public function apiGetAll(): JsonResponse
    {
        $players = $this->playerService->getAll();
        $formatted = $this->playerService->formatForFrontend($players);

        return response()->json($formatted);
    }

    public function apiGetActive(): JsonResponse
    {
        $players = $this->playerService->getActive();
        $formatted = $this->playerService->formatForFrontend($players);

        return response()->json($formatted);
    }

    public function apiGetStats(Player $player): JsonResponse
    {
        $stats = $this->playerService->getStats($player);

        return response()->json($stats);
    }
}
