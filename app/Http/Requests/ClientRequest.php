<?php

namespace App\Http\Requests;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'number' => [
                'required',
                'string',
                'regex:/^[2-4][0-9]{7}$/',
                'unique:clients,number,' . $id
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
    'number.regex' => 'Le numéro de téléphone doit commencer par 2, 3 ou 4 suivi de 7 chiffres.',
    'number.unique' => 'Ce numéro de téléphone est déjà enregistré.',
];

    }
}
