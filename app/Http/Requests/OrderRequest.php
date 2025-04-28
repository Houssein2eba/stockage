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
            'client.id' => ['nullable', 'exists:clients,id'],
            'payment_id' => $isClient ? ['nullable', 'exists:payments,id'] : ['required', 'exists:payments,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'notes' => ['nullable', 'string', 'max:1000']
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
            'client.id.exists' => 'Client not found',
            'payment_id.exists' => 'Payment method not found',
            'items.*.product.id.exists' => 'Product not found',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.integer' => 'Quantity must be an integer',
            'items.*.quantity.min' => 'Quantity must be at least :min',
            'items.*.quantity.max' => 'Quantity cannot exceed :max',
        ];
    }
}

