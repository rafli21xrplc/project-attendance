<?php

namespace App\Http\Requests\permission;

use Illuminate\Foundation\Http\FormRequest;

class permissionRequest extends FormRequest
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
            'description' => 'required|string|max:500',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 500 characters.',
            'file.required' => 'The file field is required.',
            'file.image' => 'The file must be an image.',
            'file.mimes' => 'The file must be a type of: jpeg, png, jpg, gif.',
            'file.max' => 'The file may not be greater than 2MB.',
        ];
    }
}
