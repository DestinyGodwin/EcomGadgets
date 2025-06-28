<?php

namespace App\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class FeaturedProductRequest extends FormRequest
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
            'plan_id' => ['required','exists:featured_product_plans,id'],
            'product_id' => ['required','exists:products,id'],
        ];
    }
}
