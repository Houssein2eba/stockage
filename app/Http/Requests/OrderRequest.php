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

        // dd($this->all());

        $isClient = $this->client===null ? false : true;


        return [
             // client data
        'client.id' => ['nullable', 'uuid', 'exists:clients,id'],
        'client.name' => ['nullable', 'string'],
        'client.number' => ['nullable', 'string'],

        // paid field
        'paid' => $isClient ? ['nullable', 'boolean'] : ['required', 'boolean'],

        // items array
        'items' => ['required', 'array', 'min:1'],
        'items.*.stock.id' => ['required', 'uuid', 'exists:stocks,id'],
        'items.*.products' => ['required', 'array', 'min:1'],

        // products inside each item
        'items.*.products.*.product.id' => ['required', 'uuid', 'exists:products,id'],
        'items.*.products.*.quantity' => ['required', 'integer', 'min:1'],
        ];


    }
    public function messages()
    {
        return [
 // Client validation

        'client.id.uuid' => 'Client ID must be a valid UUID.',
        'client.id.exists' => 'The selected client does not exist.',
        
        'client.name.string' => 'Client name must be a string.',

        'client.number.string' => 'Client number must be a string.',

        // Paid field
        'paid.required' => 'Payment status is required.',
        'paid.boolean' => 'The paid field must be true or false.',

        // Items array
        'items.required' => 'Items list is required.',
        'items.array' => 'Items must be an array.',
        'items.min' => 'At least one item is required.',

        // Stock inside items
        'items.*.stock.id.required' => 'Stock ID is required for each item.',
        'items.*.stock.id.uuid' => 'Stock ID must be a valid UUID.',
        'items.*.stock.id.exists' => 'The selected stock does not exist.',

        // Products inside each item
        'items.*.products.required' => 'Each item must contain at least one product.',
        'items.*.products.array' => 'Products must be in an array.',
        'items.*.products.min' => 'Each item must have at least one product.',

        'items.*.products.*.product.id.required' => 'Product ID is required.',
        'items.*.products.*.product.id.uuid' => 'Product ID must be a valid UUID.',
        'items.*.products.*.product.id.exists' => 'The selected product does not exist.',

        'items.*.products.*.quantity.required' => 'Product quantity is required.',
        'items.*.products.*.quantity.integer' => 'Product quantity must be an integer.',
        'items.*.products.*.quantity.min' => 'Product quantity must be at least 1.',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */

}

