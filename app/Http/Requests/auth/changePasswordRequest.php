<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class changePasswordRequest extends FormRequest
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
    public function rules()
    {
        return [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
            'new_password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Current password is required.',
            'current_password.string' => 'Current password must be a string.',
            'current_password.min' => 'Current password must be at least 8 characters.',

            'new_password.required' => 'New password is required.',
            'new_password.string' => 'New password must be a string.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.different' => 'New password must be different from the current password.',

            'new_password_confirmation.required' => 'New password confirmation is required.',
            'new_password_confirmation.string' => 'New password confirmation must be a string.',
            'new_password_confirmation.min' => 'New password confirmation must be at least 8 characters.',
        ];
    }
}
