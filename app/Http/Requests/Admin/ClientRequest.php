<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->input('id');

        return [
            'full_name'    => ['required', 'string', 'max:255'],
            'phone'        => ['required', 'string', 'max:20'],
            'email'        => [
                'nullable',
                'email',
                Rule::unique('clients', 'email')->ignore($clientId)
            ],
            'cpf'          => [
                'nullable',
                'string',
                'size:11',
                Rule::unique('clients', 'cpf')->ignore($clientId)
            ],
            'birth_date'   => ['nullable', 'date'],
            'postal_code'  => ['nullable', 'string', 'max:10'],
            'street'       => ['nullable', 'string', 'max:255'],
            'number'       => ['nullable', 'string', 'max:20'],
            'complement'   => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:2'],
            'notes'        => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'O nome completo é obrigatório.',
            'phone.required'     => 'O telefone é obrigatório.',
            'cpf.unique'         => 'Este CPF já está cadastrado no sistema.',
            'email.unique'       => 'Este e-mail já está sendo utilizado.',
        ];
    }
}
