<?php

namespace App\Http\Requests\V1\Auth;
use App\Models\Lga;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
           'first_name' => ['sometimes', 'string', 'min:3', 'max:255'],
           'last_name' => ['sometimes', 'string', 'min:3', 'max:255'],
           'email' => ['sometimes', 'email', 'max:255',  Rule::unique(User::class)->ignore($this->user()->id) ],
           'phone' => [ 'sometimes','regex:/^\+?[0-9]{10,15}$/', Rule::unique(User::class)->ignore($this->user()->id)], 
           'profile_picture' => ['sometimes','image','max:2048'],
            'state_id' => ['sometimes', 'string', 'exists:states,id'],
            'lga_id' => ['sometimes', 'string', 'exists:lgas,id'],
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $stateId = $this->input('state_id');
        $lgaId = $this->input('lga_id');

        if ($stateId && $lgaId) {
            $exists = Lga::where('id', $lgaId)
                        ->where('state_id', $stateId)
                        ->exists();

            if (! $exists) {
                $validator->errors()->add('lga_id', 'The selected LGA does not belong to the selected state.');
            }
        }
    });
}
}
