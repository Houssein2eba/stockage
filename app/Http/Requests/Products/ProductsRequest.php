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

    ];

    return $rules;
}


public function messages()
{
    return [
        'category.*.id.required' => 'Please select at least one category.',
        'stock.*.id.required' => 'Please select at least one stock.',
        'stock.*.id.exists' => 'The selected stock does not exist.',
        'image.image' => 'The image must be a valid image file.',
        'image.max' => 'The image must not be larger than 2MB.',
        'name.required' => 'The product name is required.',
        'name.string' => 'The product name must be a string.',
    ];
}

}
