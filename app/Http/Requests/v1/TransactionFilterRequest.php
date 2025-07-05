<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;
use App\Enums\V1\TransactionType;
use App\Enums\V1\TransactionStatus;
use Illuminate\Foundation\Http\FormRequest;

class TransactionFilterRequest extends FormRequest
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
            'status' => ['nullable', Rule::enum(TransactionStatus::class)],
            'type' => ['nullable', Rule::enum(TransactionType::class)],
            'channel' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0|gte:amount_min',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'status.enum' => 'The status must be one of: ' . implode(', ', TransactionStatus::values()),
            'type.enum' => 'The type must be one of: ' . implode(', ', TransactionType::values()),
        ];
    }
}
