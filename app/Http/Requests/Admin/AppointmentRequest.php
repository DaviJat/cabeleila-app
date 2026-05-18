<?php

namespace App\Http\Requests\Admin;

use App\Models\Availability;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
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
            'client_id' => 'required|integer|exists:clients,id',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'integer|exists:services,id',
            'notes' => 'nullable|string|max:1000',
            'availability_id' => [
                'required',
                'integer',
                'exists:availabilities,id',
                // Custom rule to ensure the slot is actually free
                function ($attribute, $value, $fail) {
                    $availability = Availability::find($value);
                    if ($availability && !$availability->is_available) {
                        $fail('Este horário já foi ocupado por outro agendamento.');
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
            'client_id.required' => 'O cliente é obrigatório.',
            'client_id.exists' => 'O cliente selecionado não é válido.',
            'service_ids.required' => 'Selecione ao menos um serviço.',
            'service_ids.array' => 'O formato dos serviços é inválido.',
            'service_ids.min' => 'Selecione ao menos um serviço.',
            'service_ids.*.exists' => 'Um dos serviços selecionados não é válido.',
            'availability_id.required' => 'O horário é obrigatório.',
            'availability_id.exists' => 'O horário selecionado não existe.',
        ];
    }
}
