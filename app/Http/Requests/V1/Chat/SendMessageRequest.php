<?php

namespace App\Http\Requests\V1\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'receiver_id' => ['required', 'exists:users,id', 'different:auth'],
            'body' => ['nullable', 'string', 'required_without:images', 'max:2500'],
            'images' => ['nullable', 'array', 'min:1', `required_without:body`],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
