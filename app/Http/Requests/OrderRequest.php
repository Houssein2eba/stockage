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
        dd($this->all());

        $isClient = $this->client_id===null ? false : true;
        
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'payment_id' => $isClient ? ['nullable', 'exists:payments,id'] : ['required', 'exists:payments,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
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
            'items.required' => 'At least one item is required for the order',
            'items.array' => 'Items must be provided as a list',
            'items.min' => 'At least one item is required for the order',
            'items.*.product_id.required' => 'Please select a product for each item',
            'items.*.product_id.exists' => 'One or more selected products do not exist',
            'items.*.quantity.required' => 'Please specify quantity for each item',
            'items.*.quantity.integer' => 'Quantity must be a whole number',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.quantity.max' => 'Quantity cannot exceed 1000 units',
            'client_id.exists' => 'The selected client does not exist',
            'payment_id.required' => 'Payment method is required for non-client orders',
            'payment_id.exists' => 'The selected payment method is invalid',
            'notes.string' => 'Notes must be text',
            'notes.max' => 'Notes cannot exceed 1000 characters'
        ];
    }
}

