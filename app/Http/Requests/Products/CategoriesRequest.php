<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriesRequest extends FormRequest
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
            'name'=>['required','string','max:255',Rule::unique('categories','name')->ignore($id)],
            'description'=>'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
    'name.required' => 'Le champ nom est obligatoire.',
    'name.string' => 'Le champ nom doit être une chaîne de caractères.',
    'name.max' => 'Le champ nom ne doit pas dépasser 255 caractères.',
    'name.unique' => 'Ce nom est déjà utilisé.',
    'description.string' => 'Le champ description doit être une chaîne de caractères.',
    'description.max' => 'Le champ description ne doit pas dépasser 255 caractères.',
];

    }
}
