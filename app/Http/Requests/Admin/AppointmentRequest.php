<?php

namespace App\Http\Requests\Admin;

use App\Models\Availability;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
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
            // If an ID is provided, the request is treated as an update process
            'id' => 'nullable|integer|exists:appointments,id',
            'status' => 'nullable|string|in:pending,confirmed,canceled',

            // Creation-specific fields are required only when no ID is supplied
            'client_id' => 'required_without:id|integer|exists:clients,id',
            'service_ids' => 'required_without:id|array|min:1',
            'service_ids.*' => 'integer|exists:services,id',
            'notes' => 'nullable|string|max:1000',
            'availability_id' => [
                'required_without:id',
                'integer',
                'exists:availabilities,id',
                // Custom rule to prevent double-booking exclusively during the creation phase
                function ($value, $fail) {
                    if (! $this->filled('id')) {
                        $availability = Availability::find($value);

                        if ($availability && ! $availability->is_available) {
                            $fail('Este horário já foi ocupado por outro agendamento.');
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
            'client_id.required_without' => 'O cliente é obrigatório.',
            'client_id.exists' => 'O cliente selecionado não é válido.',
            'service_ids.required_without' => 'Selecione ao menos um serviço.',
            'service_ids.array' => 'O formato dos serviços é inválido.',
            'service_ids.min' => 'Selecione ao menos um serviço.',
            'service_ids.*.exists' => 'Um dos serviços selecionados não é válido.',
            'availability_id.required_without' => 'O horário é obrigatório.',
            'availability_id.exists' => 'O horário selecionado não existe.',
        ];
    }
}
