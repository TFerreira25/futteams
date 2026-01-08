<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTeamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_ids' => ['required', 'array', 'min:4'],
            'player_ids.*' => ['integer', 'exists:players,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'player_ids.required' => 'Selecione pelo menos 4 jogadores',
            'player_ids.min' => 'Precisa de pelo menos 4 jogadores',
            'player_ids.*.exists' => 'Um ou mais jogadores selecionados não existem',
        ];
    }
}
