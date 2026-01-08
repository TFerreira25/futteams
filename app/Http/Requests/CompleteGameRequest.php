<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team1_goals' => ['required', 'integer', 'min:0'],
            'team2_goals' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'team1_goals.required' => 'Golos Team 1 é obrigatório',
            'team2_goals.required' => 'Golos Team 2 é obrigatório',
        ];
    }
}
