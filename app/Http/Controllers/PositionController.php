<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{

    public function index(): Response
    {
        $positions = Position::orderBy('sort_order')->get();

        return Inertia::render('positions/index', [
            'positions' => $positions,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('positions/form', [
            'position' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:positions,code',
            'name' => 'required|string|max:255',
            'max_per_team' => 'required|integer|min:1|max:11',
            'sort_order' => 'required|integer|min:0',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')
            ->with('success', 'Posição criada com sucesso!');
    }

    public function edit(Position $position): Response
    {
        return Inertia::render('positions/form', [
            'position' => $position,
        ]);
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:positions,code,' . $position->id,
            'name' => 'required|string|max:255',
            'max_per_team' => 'required|integer|min:1|max:11',
            'sort_order' => 'required|integer|min:0',
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')
            ->with('success', 'Posição atualizada com sucesso!');
    }

    public function destroy(Position $position)
    {
        try {
            $position->delete();
            return redirect()->route('positions.index')
                ->with('success', 'Posição eliminada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('positions.index')
                ->with('error', 'Erro ao eliminar posição. Pode estar associada a jogadores.');
        }
    }
}
