<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'id'               => ['nullable', 'integer', 'exists:services,id'],
            'name'             => ['required', 'string', 'max:50'],
            'description'      => ['required', 'string', 'max:250'],
            'price'            => ['required', 'numeric', 'min:0', 'max:10000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
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
            'name.required'             => 'O nome do serviço é obrigatório.',
            'name.max'                  => 'O nome do serviço não pode exceder 50 caracteres.',
            'description.required'      => 'A descrição é obrigatória.',
            'description.max'           => 'A descrição não pode exceder 250 caracteres.',

            'price.required'            => 'O preço é obrigatório.',
            'price.numeric'             => 'O preço deve ser um valor numérico.',
            'price.min'                 => 'O preço não pode ser negativo.',
            'price.max'                 => 'O preço máximo permitido é $10,000.00.',

            'duration_minutes.required' => 'A duração é obrigatória.',
            'duration_minutes.integer'  => 'A duração deve ser um valor inteiro.',
            'duration_minutes.min'      => 'A duração mínima é de 5 minutos.',
            'duration_minutes.max'      => 'A duração não pode exceder 480 minutos (8 horas).',
        ];
    }
}
