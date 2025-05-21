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
            'client.id' => ['nullable', 'exists:clients,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'paid' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000']
        ];


    }
    public function messages()
    {
        return [
            'client.id.exists' => 'Client not found',
            'payment.id.exists' => 'Payment method not found',
            'payment.id.required' => 'Payment method is required for non-registered clients',

            'items.*.product.id.exists' => 'Product not found',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.integer' => 'Quantity must be an integer',
            'items.*.quantity.min' => 'Quantity must be at least :min',
            'items.*.quantity.max' => 'Quantity cannot exceed :max',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */

}

