<?php

namespace App\Http\Requests\Products;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductsRequest extends FormRequest
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
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
    $productId = $this->route('id'); // for PUT/PATCH



    // Validation rules
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
        'quantity' => ['required', 'integer', 'min:0'],
        'cost' => ['required', 'numeric', 'min:0'],
        'category' => ['required', 'array', 'min:1'],
        'category.*.id' => ['required', 'uuid'],
        'stock' => ['required', 'array', 'min:1'],
        'stock.id' => ['required', 'uuid','exists:stocks,id'],
        'expiry_date' => ['nullable', 'date'],
        'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

    ];

    return $rules;
}


public function messages()
{
    return [
    'category.*.id.required' => 'Veuillez sélectionner au moins une catégorie.',
    'stock.*.id.required' => 'Veuillez sélectionner au moins un stock.',
    'stock.*.id.exists' => 'Le stock sélectionné n’existe pas.',
    'image.image' => 'L’image doit être un fichier image valide.',
    'image.max' => 'L’image ne doit pas dépasser 2 Mo.',
    'name.required' => 'Le nom du produit est obligatoire.',
    'name.string' => 'Le nom du produit doit être une chaîne de caractères.',
];


}

}
