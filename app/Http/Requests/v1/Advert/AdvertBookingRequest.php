<?php

namespace App\Http\Requests\V1\Advert;

use Illuminate\Foundation\Http\FormRequest;

class AdvertBookingRequest extends FormRequest
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
            'plan_id' => ['required','exists:advert_plans,id'],
            'state_id' => ['required','exists:states,id'],
            'starts_at' => ['required', 'date', 'after_or_equal:today'],       
        ];
    }
}
