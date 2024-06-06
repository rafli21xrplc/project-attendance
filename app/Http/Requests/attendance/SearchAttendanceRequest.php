<?php

namespace App\Http\Requests\attendance;

use Illuminate\Foundation\Http\FormRequest;

class SearchAttendanceRequest extends FormRequest
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
            'date' => 'required|date',
            'classroom_id' => 'required|exists:class_room,id', 
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date format.',
            'classroom_id.required' => 'The classroom ID field is required.',
            'classroom_id.exists' => 'The selected classroom ID is invalid.',
        ];
    }
}
