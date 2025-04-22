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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'client' => ['nullable', 'array'],
            'client.id' => ['nullable', 'exists:clients,id'],
            'payment' => ['nullable', 'array'],
            'payment.id' => ['nullable', 'exists:payments,id'],
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
            'items.required' => 'At least one item is required for the sale',
            'items.min' => 'At least one item is required for the sale',
            'items.*.product.required' => 'Please select a product for each item',
            'items.*.quantity.required' => 'Please specify quantity for each item',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.quantity.max' => 'Quantity cannot exceed 10000 units',
            'client.id.exists' => 'The selected client is invalid',
            'payment.id.exists' => 'The selected payment method is invalid',
            'status.in' => 'Invalid order status'
        ];
    }
}

