<?php
namespace App\Http\Requests\v1\admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPlanRequest extends FormRequest
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
            'name'          => ['sometimes', 'string', 'max:255'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'description'   => ['sometimes', 'string', 'min:3', 'max:255'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
