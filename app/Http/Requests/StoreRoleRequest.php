<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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

        return [
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array|min:1|required',

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom du role est obligatoire.',
            'name.string' => 'Le nom du role doitêtre une chaîne de caractères.',
            'name.max' => 'Le nom du role ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Ce role existe déjà.',
            'permissions.required' => 'Veuillez sélectionner au moins une permission.',
            'permissions.min' => 'Veuillez sélectionner au moins une permission.',
        ];
    }
}
