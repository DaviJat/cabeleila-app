<?php

namespace App\Http\Requests\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Garante que o e-mail seja único, exceto para o próprio dono do perfil
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Mensagens personalizadas para o Toast/Frontend
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'Digite um e-mail válido.',
            'email.unique'   => 'Este e-mail já está sendo utilizado por outra conta.',
        ];
    }
}
