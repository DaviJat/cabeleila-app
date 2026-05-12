<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer essa requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            // O ID é necessário apenas para validar a existência no update
            'id' => 'nullable|exists:availabilities,id',
            'date' => 'required|date|after_or_equal:today', // Garante que a data não seja passada
            'hour' => [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', // Valida formato HH:mm
            ],
        ];
    }
    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'date.required' => ' A data é obrigatória.',
            'date.after_or_equal' => 'Não é possível cadastrar horários em datas passadas.',
            'hour.required' => 'O horário é obrigatório.',
            'hour.regex' => 'O formato do horário deve ser HH:mm.',
        ];
    }
}
