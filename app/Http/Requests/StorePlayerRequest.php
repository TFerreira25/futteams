<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:players,email'],
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
