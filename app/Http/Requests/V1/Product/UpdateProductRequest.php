<?php

namespace App\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255', 'min:3'],
            'description' => ['sometimes', 'string', 'min:3', 'max:1000'],
            'specifications' => ['nullable', 'array'],
            'specifications.*' => ['required', 'string', 'min:3', 'max:255'],
            'brand' => ['sometimes', 'string', 'max:100'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'removed_images' => ['nullable', 'array'],
            'removed_images.*' => ['string', 'exists:product_images,id'],
        ];
    }
}
