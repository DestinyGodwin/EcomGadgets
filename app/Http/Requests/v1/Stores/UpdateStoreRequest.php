<?php

namespace App\Http\Requests\V1\Stores;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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
            'store_name' => ['sometimes', 'string', 'min:3', 'max:255', 'unique:stores,store_name,' . $this->store->id],
            'store_image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'store_description' => ['sometimes', 'string', 'min:3', 'max:233'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'regex:/^\+?[0-9]{10,15}$/'],
            'state_id' => ['sometimes', 'exists:states,id'],
            'lga_id' => ['sometimes', 'exists:lgas,id'],
            'address' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
