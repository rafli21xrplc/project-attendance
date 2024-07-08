<?php

namespace App\Http\Requests\promotedStudent;

use Illuminate\Foundation\Http\FormRequest;

class updateRequest extends FormRequest
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
            'student_id' => 'required|exists:student,id',
            'type_class_id' => 'required|exists:type_class,id',
            'classroom_id' => 'required|exists:class_room,id',
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
            'student_id.required' => 'Student ID is required.',
            'student_id.exists' => 'The selected student ID does not exist.',
            'type_class_id.required' => 'Type Class ID is required.',
            'type_class_id.exists' => 'The selected type class ID does not exist.',
        ];
    }
}
