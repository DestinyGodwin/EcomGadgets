<?php

namespace App\Http\Requests\V1\Product;

use DB;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AddProductsToSubscriptionRequest extends FormRequest {
    /**
    * Determine if the user is authorized to make this request.
    */

    public function authorize(): bool {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */

    public function rules(): array {
        return [
            'subscription_id' => 'required|string|exists:featured_product_subscriptions,id',
            'product_ids' => 'required|array|min:1|max:30',
            'product_ids.*' => 'required|string|exists:products,id',
        ];
    }

    public function withValidator( Validator $validator ): void {
        $validator->after( function ( $validator ) {
            if ( $this->has( 'subscription_id' ) && $this->has( 'product_ids' ) ) {
                $existing = DB::table( 'featured_subscription_products' )
                ->where( 'subscription_id', $this->subscription_id )
                ->whereIn( 'product_id', $this->product_ids )
                ->pluck( 'product_id' )
                ->toArray();

                if ( !empty( $existing ) ) {
                    $duplicates = implode( ', ', $existing );
                    $validator->errors()->add(
                        'product_ids',
                        "The following products are already added to this subscription"
                    );
                }
            }
        }
    );
}

    public function messages(): array {
        return [
            'subscription_id.required' => 'Subscription ID is required.',
            'subscription_id.exists' => 'Invalid subscription.',
            'product_ids.required' => 'At least one product must be selected.',
            'product_ids.array' => 'Product IDs must be an array.',
            'product_ids.min' => 'At least one product must be selected.',
            'product_ids.max' => 'You can add a maximum of 30 products at once.',
            'product_ids.*.exists' => 'One or more products are invalid.',
        ];
    }
}
