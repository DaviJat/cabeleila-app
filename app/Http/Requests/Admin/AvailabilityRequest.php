<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|numeric|exists:availabilities,id',
            'date' => 'required|date',
            'hour' => [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', // Validates HH:mm format
                // Ensure the date and hour combination is unique, ignoring the current record ID during updates
                Rule::unique('availabilities')
                    ->where(fn($query) => $query->where('date', $this->input('date'))->where('hour', $this->input('hour')))
                    ->ignore($this->input('id')),
                // Prevent the creation of availability slots in the past
                function ($attribute, $value, $fail) {
                    if ($this->input('date') && $value) {
                        $targetDateTime = Carbon::parse(Carbon::parse($this->input('date'))->toDateString() . ' ' . $value);

                        // If the target date and time has already passed, validation fails
                        if ($targetDateTime->isPast()) {
                            $fail('Não é possível cadastrar horários em datas ou horas passadas.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
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
