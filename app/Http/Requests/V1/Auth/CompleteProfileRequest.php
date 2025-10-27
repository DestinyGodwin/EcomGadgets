<?php

namespace App\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'regex:/^\+?[0-9]{10,15}$/', 'unique:users,phone'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'state_id' => ['required', 'string', 'exists:states,id'],
            'lga_id' => ['required', 'string', 'exists:lgas,id'],
        ];
    }
}
