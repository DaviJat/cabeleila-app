<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

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
        // Captura o ID garantindo valor numérico para evitar erro de sintaxe no Postgres
        $id = is_numeric($this->id) ? $this->id : null;

        return [
            // O ID é necessário apenas para validar a existência no update
            'id' => 'nullable|numeric|exists:availabilities,id',
            'date' => 'required|date',
            'hour' => [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', // Valida formato HH:mm
                // Valida se a combinação data + hora já existe, ignorando o próprio ID em caso de edição
                Rule::unique('availabilities')->where(function ($query) {
                    return $query->where('date', $this->date)
                        ->where('hour', $this->hour);
                })->ignore($id),
                // Valida se o momento selecionado (data e hora) já passou
                function ($attribute, $value, $fail) {
                    if ($this->date && $value) {
                        // Limpa a data de horas residuais e valida o momento completo contra o agora
                        $cleanDate = Carbon::parse($this->date)->toDateString();
                        if (Carbon::parse($cleanDate . ' ' . $value)->isPast()) {
                            $fail('Não é possível cadastrar horários em datas ou horas passadas.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'A data é obrigatória.',
            'date.date' => 'Informe uma data válida.',
            'hour.required' => 'O horário é obrigatório.',
            'hour.regex' => 'O formato do horário deve ser HH:mm.',
            'hour.unique' => 'Este horário já está cadastrado para o dia selecionado.',
        ];
    }
}
