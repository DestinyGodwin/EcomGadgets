<?php

namespace App\Http\Requests\V1\Expo;

use Illuminate\Foundation\Http\FormRequest;
use NotificationChannels\Expo\ExpoPushToken;

class StoreDeviceRequest extends FormRequest
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
        'device_id' => ['required', 'string', 'max:255'],
        'token'     => ['required', ExpoPushToken::rule()],
    ];
    }
}

 
