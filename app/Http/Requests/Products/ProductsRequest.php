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

        return [
            'name'=>['required','string','min:3','max:255'],
            'description'=>['string','nullable'],
            'category'=>['required','array'],
            'category.*.name'=>$this->isMethod('PUT')?Rule::unique('categories','name')->ignore($this->route('id')):'required|exists:categories,name',
            'image'=>['required','image'],
            'price'=>['required','min:10','max:1000000','numeric'],
            'quantity'=>['required','min:1','max:10000','integer'],
            'min_quantity'=>['required','min:1','integer'],
        ];
    }
}
