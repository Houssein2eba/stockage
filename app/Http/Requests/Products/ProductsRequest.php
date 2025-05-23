<?php

namespace App\Http\Requests\Products;

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
    $productId = $this->route('id');

    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'cost' => 'required|numeric|min:0',
        'image' => 'nullable|image|max:2048',
        'expiry_date' => 'nullable|date',

        // Validate categories (each must have an id)
        'category' => 'required|array|min:1',
        'category.*.id' => 'required|uuid|exists:categories,id',

        // Validate stocks with quantity and expiry_date
        'stockQuantities' => 'required|array|min:1',
        'stockQuantities.*.stock.id' => 'required|uuid|exists:stocks,id',
        'stockQuantities.*.quantity' => 'required|integer|min:0',
        'stockQuantities.*.expiry_date' => 'nullable|date',





    ];
}

public function messages()
{
    return [
        'name.required' => 'The product name is required.',
        'name.string' => 'The product name must be a string.',
        'name.max' => 'The product name may not be greater than :max characters.',
        'description.string' => 'The product description must be a string.',
        'price.required' => 'The product price is required.',
        'price.numeric' => 'The product price must be a number.',
        'price.min' => 'The product price must be at least :min.',
        'cost.required' => 'The product cost is required.',
        'cost.numeric' => 'The product cost must be a number.',
        'cost.min' => 'The product cost must be at least :min.',
        'image.image' => 'The product image must be an image.',
        'image.max' => 'The product image may not be greater than :max kilobytes.',
        'category.required' => 'The product category is required.',
        'category.array' => 'The product category must be an array.',
        'category.min' => 'The product must have at least one category.',
        'category.*.id.required' => 'The category  is required.',
        'category.*.id.uuid' => 'The category must be a valid .',
        'category.*.id.exists' => 'The selected category does not exist.',
        'stockQuantities.required' => 'The stock quantities are required.',
       'stockQuantities.array' => 'The stock quantities must be an array.',
       'stockQuantities.min' => 'The product must have at least one stock quantity.',
      'stockQuantities.*.stock.id.required' => 'The stock ID is required.',
      'stockQuantities.*.stock.id.uuid' => 'The stock  must be  valid .',
      'stockQuantities.*.stock.id.exists' => 'The selected stock does not exist.',
      'stockQuantities.*.quantity.required' => 'The stock quantity is required.',
     'stockQuantities.*.quantity.integer' => 'The stock quantity must be an integer.',
     'stockQuantities.*.quantity.min' => 'The stock quantity must be at least :min.',
     'stockQuantities.*.expiry_date.date' => 'The expiry date must be a valid date.',
    ];
}

}
