<?php

namespace App\Http\Requests\V1\Stores;

use Illuminate\Foundation\Http\FormRequest;

class CreateStoreRequest extends FormRequest
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
            'store_name' => ['required', 'string', 'min:3', 'max:255', 'unique:stores,store_name'],
            'store_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'store_description' => ['required','string', 'min:3', 'max:233'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/'],
            'state_id' => ['required', 'exists:states,id'],
            'lga_id' => ['required', 'exists:lgas,id'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }
}
