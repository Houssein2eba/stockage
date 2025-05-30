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



        $isClient = $this->client===null ? false : true;


        return [
             // client data
        'client.id' => ['nullable', 'uuid', 'exists:clients,id'],
        'stock.id' => ['required', 'uuid', 'exists:stocks,id'],

        // paid field
        'paid' => $isClient ? ['nullable', 'boolean'] : ['required', 'boolean'],

        // items array
        'products' => ['required', 'array', 'min:1'],
        'products.*.product.id' => ['required', 'uuid','exists:products,id'],
        'products.*.quantity' => ['required', 'integer', 'min:1'],

        ];


    }
    public function messages()
    {
        return [
            'client.id.exists' => 'The selected client does not exist.',
            'stock.id.exists' => 'The selected stock does not exist.',
            'products.*.product.id.exists' => 'The selected product does not exist.',
            'products.required' => 'At least one product is required.',
            'products.*.quantity.min' => 'The quantity must be at least 1.',
            'paid.boolean' => 'The paid field must be true or false.',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */

}

