<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone'       => $this->phone ? preg_replace('/[^0-9]/', '', $this->phone) : null,
            'cpf'         => $this->cpf ? preg_replace('/[^0-9]/', '', $this->cpf) : null,
            'postal_code' => $this->postal_code ? preg_replace('/[^0-9]/', '', $this->postal_code) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $clientId = $this->input('id');

        return [
            'full_name'    => ['required', 'string', 'max:100'],
            'phone'        => ['required', 'string', 'digits_between:10,11'],
            'email'        => [
                'nullable',
                'email',
                'max:100',
                // Ensure email uniqueness while ignoring the current record ID
                Rule::unique('clients', 'email')->ignore($clientId)
            ],
            'cpf'          => [
                'nullable',
                'string',
                'digits:11',
                // Ensure CPF uniqueness while ignoring the current record ID
                Rule::unique('clients', 'cpf')->ignore($clientId)
            ],
            'birth_date'   => ['nullable', 'date'],
            'postal_code'  => ['nullable', 'string', 'digits:8'],
            'street'       => ['nullable', 'string', 'max:100'],
            'number'       => ['nullable', 'string', 'max:10'],
            'complement'   => ['nullable', 'string', 'max:50'],
            'neighborhood' => ['nullable', 'string', 'max:50'],
            'city'         => ['nullable', 'string', 'max:50'],
            'state'        => ['nullable', 'string', 'max:50'],
            'notes'        => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'full_name.required'   => 'O nome completo é obrigatório.',
            'full_name.max'        => 'O nome não pode passar de 100 caracteres.',
            'phone.required'       => 'O número de telefone é obrigatório.',
            'phone.digits_between' => 'O telefone deve conter apenas números, incluindo o DDD (10 ou 11 dígitos).',
            'cpf.digits'           => 'O CPF deve conter exatamente 11 números, sem traços ou pontos.',
            'cpf.unique'           => 'Este CPF já está cadastrado no sistema.',
            'email.unique'         => 'Este endereço de e-mail já está em uso.',
            'postal_code.digits'   => 'O CEP deve conter exatamente 8 números, sem traços.',
            'notes.max'            => 'As observações não podem ter mais de 255 caracteres.',
        ];
    }
}
