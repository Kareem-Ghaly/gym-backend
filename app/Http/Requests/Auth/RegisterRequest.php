<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:user,coach'],
            'gender' => ['nullable', 'string', 'in:male,female'],
            // Coach specific fields
            'bio' => ['required_if:role,coach',  'string'],
            'specialization' => ['required_if:role,coach',  'string'],
            'experience_years' => ['required_if:role,coach',  'integer', 'min:0'],
            'certification' => ['required_if:role,coach',  'string'],
        ];
    }
}

