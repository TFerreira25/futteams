<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'total_players' => ['nullable', 'integer', 'min:4', function ($attribute, $value, $fail) {
                if ($value % 2 !== 0) {
                    $fail('O número de jogadores deve ser par.');
                }
            }],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'A data é obrigatória',
            'date.date' => 'Formato de data inválido',
            'total_players.integer' => 'O número de jogadores deve ser um número inteiro',
            'total_players.min' => 'Mínimo 4 jogadores',
        ];
    }
}
