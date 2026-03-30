<?php

namespace App\Http\Requests\Coach;

use Illuminate\Foundation\Http\FormRequest;

class AcceptVipRequestRequest extends FormRequest
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
            'diet_plan' => ['required', 'array'],
            'diet_plan.title' => ['required', 'string', 'max:255'],
            'diet_plan.description' => ['nullable', 'string'],
            // 'diet_plan.content' => ['required', 'array'],
            'workout_plan' => ['required', 'array'],
            'workout_plan.title' => ['required', 'string', 'max:255'],
            'workout_plan.description' => ['nullable', 'string'],
            // 'workout_plan.content' => ['required', 'array'],
        ];
    }
}


