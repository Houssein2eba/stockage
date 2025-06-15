<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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


        $isClient = $this->client==null ? false : true;
        // dd($this);


        return [
        'client' => ['nullable', 'array'],
        'client.id' => ['required_with:client', 'uuid'],
        'stock' => ['required', 'array'],
        'stock.id' => ['required', 'uuid'], // ajuste selon les champs nécessaires
        'products' => ['required', 'array', 'min:1'],
        'products.*.product' => ['required', 'array'],
        'products.*.product.id' => ['required', 'uuid'],
        'products.*.quantity' => ['required', 'integer', 'min:1'],
        'products.*.total_amount' => ['required', 'numeric'],
        'paid' => ['required', 'boolean'],
        'order_total_amount' => ['required', 'numeric'],
        ];


    }
    public function messages()
    {
        return [

        // stock.id
        'stock.id.required' => 'Le stock est obligatoire.',
        'stock.id.exists' => 'Le stock sélectionné n\'existe pas.',

        // paid
        'paid.in' => 'payement est obligatoire pour les non-clients.',
        'paid.boolean' => 'Le champ payé doit être vrai  ou faux .',

        // products
        'products.required' => 'La liste des produits est obligatoire.',
        'products.array' => 'Le champ produits doit être une liste.',
        'products.min' => 'Vous devez sélectionner au moins un produit.',

        // products.*.product.id
        'products.*.product.id.required' => 'Chaque produit doit avoir un identifiant.',

        'products.*.product.id.exists' => 'Le produit sélectionné n\'existe pas.',

        // products.*.quantity
        'products.*.quantity.required' => 'La quantité est obligatoire pour chaque produit.',
        'products.*.quantity.integer' => 'La quantité doit être un nombre entier.',
        'products.*.quantity.min' => 'La quantité doit être d\'au moins 1.',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */

}

