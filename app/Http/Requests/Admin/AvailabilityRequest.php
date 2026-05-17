<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|numeric|exists:availabilities,id',
            'date' => 'required|date',
            'hour' => [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', // Validates HH:mm format
                // Ensures the date + hour combination is unique, ignoring current ID on update
                Rule::unique('availabilities')
                    ->where(fn($query) => $query->where('date', $this->date)->where('hour', $this->hour))
                    ->ignore($this->id),
                // Validates that the chosen date and time has not already passed
                function ($attribute, $value, $fail) {
                    if ($this->date && $value) {
                        $targetDateTime = Carbon::parse(Carbon::parse($this->date)->toDateString() . ' ' . $value);
                        // If the target date and time is in the past, validation fails
                        if ($targetDateTime->isPast()) {
                            $fail('Não é possível cadastrar horários em datas ou horas passadas.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
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
