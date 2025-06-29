<?php

namespace App\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            // 'specifications' => ['nullable', 'array'],
            // 'specifications.*' => ['required', 'string', 'min:3', 'max:255'],
            // 'brand' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
{
    return [
        'category_id.required' => 'The category is required.',
        'category_id.exists' => 'The selected category does not exist.',

        'name.required' => 'The product name is required.',
        'name.string' => 'The product name must be a string.',
        'name.min' => 'The product name must be at least :min characters.',
        'name.max' => 'The product name may not be greater than :max characters.',

        'description.required' => 'The product description is required.',
        'description.string' => 'The description must be a string.',
        'description.min' => 'The description must be at least :min characters.',
        'description.max' => 'The description may not be greater than :max characters.',

        'price.required' => 'The price is required.',
        'price.numeric' => 'The price must be a number.',
        'price.min' => 'The price must be at least :min.',

        'wholesale_price.numeric' => 'The wholesale price must be a number.',
        'wholesale_price.min' => 'The wholesale price must be at least :min.',

        'images.required' => 'At least one product image is required.',
        'images.array' => 'The images must be an array.',
        'images.min' => 'You must upload at least :min image(s).',

        'images.*.image' => 'Each uploaded file must be an image.',
        'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif.',
        'images.*.max' => 'Each image must not be larger than 2MB.',
    ];
}
}
