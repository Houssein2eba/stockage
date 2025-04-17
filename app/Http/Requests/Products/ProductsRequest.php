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
        'name' => [
            'required',
            'string',
            'min:3',
            'max:255',
            Rule::unique('products', 'name')->ignore($productId)
        ],

        'description' => [
            'nullable',
            'string',
            'max:2000'
        ],

        'category' => [
            'required',
            'array',
            'min:1'
        ],

        'category.*.id' => [
            'required',
            'distinct', // prevent duplicates
            'exists:categories,id'
        ],

        'image' => [
            $isUpdate ? 'nullable' : 'required',
            'image',
            'mimes:jpeg,png,jpg,gif,webp',
            'max:2048',
            'dimensions:min_width=100,min_height=100'
        ],

        'price' => [
            'required',
            'numeric',
            'min:0.1',
            'max:1000000',
            'regex:/^\d+(\.\d{1,2})?$/'
        ],

        'quantity' => [
            'required',
            'integer',
            'min:0',
            'max:10000'
        ],

        'min_quantity' => [
            'required',
            'integer',
            'min:1',
            'max:1000',
            'lte:quantity'
        ]
    ];
}



}
