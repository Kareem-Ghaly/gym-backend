<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeToVipRequest extends FormRequest
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
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'height' => ['required', 'numeric', 'min:50', 'max:250'], // in cm
            'weight' => ['required', 'numeric', 'min:20', 'max:300'], // in kg
            'goal' => ['required', 'string', 'in:lose,gain,maintain'],
            'activity_level' => ['required', 'string', 'in:sedentary,light,moderate,active,very_active'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'coach_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}


