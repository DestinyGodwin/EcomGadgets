<?php

namespace App\Http\Requests\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDummyAdvertRequest extends FormRequest
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
        'state_id' => ['sometimes', 'exists:states,id'],
        'title' => ['sometimes', 'string', 'max:255'],
        'link' => ['nullable', 'url', 'max:255'],
        'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        'starts_at' => ['sometimes', 'date'],
        'ends_at' => ['sometimes', 'date', 'after:starts_at'],
    ];
    }
}
