<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPlayerEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'player_id' => ['required', 'integer', 'exists:players,id'],
            'event_type' => ['required', 'in:goal,assist,yellow_card,red_card,goals_conceded'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'player_id.required' => 'Jogador é obrigatório',
            'player_id.exists' => 'Jogador não existe',
            'event_type.required' => 'Tipo de evento é obrigatório',
            'event_type.in' => 'Tipo de evento inválido',
            'quantity.required' => 'Quantidade é obrigatória',
            'quantity.min' => 'Quantidade deve ser pelo menos 1',
        ];
    }
}
