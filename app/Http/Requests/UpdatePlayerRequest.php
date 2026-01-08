<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $playerId = $this->route('player');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', "unique:players,email,{$playerId}"],
            'position_id' => ['required', 'exists:positions,id'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'position_id.required' => 'A posição é obrigatória',
            'position_id.exists' => 'A posição selecionada não existe',
            'email.unique' => 'Este email já existe',
        ];
    }
}
