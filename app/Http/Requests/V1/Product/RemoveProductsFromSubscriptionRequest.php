<?php

namespace App\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class RemoveProductsFromSubscriptionRequest extends FormRequest
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
            'subscription_id' => 'required|string|exists:featured_product_subscriptions,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|string|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription ID is required.',
            'subscription_id.exists' => 'Invalid subscription.',
            'product_ids.required' => 'At least one product must be selected.',
            'product_ids.array' => 'Product IDs must be an array.',
            'product_ids.min' => 'At least one product must be selected.',
            'product_ids.*.exists' => 'One or more products are invalid.',
        ];
    }
}
