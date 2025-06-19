<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $id=$this->route('id') ?? null;

        return [
            'name' => 'required',
            'email' => ['required','email',Rule::unique('users','email')
            ->ignore($id)],
            'number'=>['required','regex:/^([2-4][0-9]{7})$/',Rule::unique('users','number')->ignore($id)],
            'password' => $this->isMethod('PUT') ? 'sometimes|confirmed' : 'required|confirmed',
            'password_confirmation' => 'sometimes|required_with:password',
            'role'=>'required|array',
            'role.name'=>'exists:roles,name',
        ];
    }

    public function messages()
    {
        return [
            'role.name.exists' => 'Le role n\'existe pas',
            'name.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse email valide',
            'email.unique' => 'L\'email est déjà utilisé',
            'number.required' => 'Le numéro est obligatoire',
            'number.regex' => 'Le numéro doit commencer par 2, 3 ou 4 suivi de 7 chiffres',
            'number.unique' => 'Le numéro est déjà utilisé',
            'password.required' => 'Le mot de passe est obligatoire',

        ];
    }
}
