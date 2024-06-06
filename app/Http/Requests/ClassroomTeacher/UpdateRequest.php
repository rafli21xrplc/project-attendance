<?php

namespace App\Http\Requests\ClassroomTeacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'class_id' => 'required|exists:class_room,id',
            'teacher_id' => 'required|exists:teacher,id',
        ];
    }

    public function messages()
    {
        return [
            'class_id.required' => 'classroom ID harus diisi.',
            'class_id.exists' => 'classroom ID yang dimasukkan tidak valid.',
            'teacher_id.required' => 'Teacher ID harus diisi.',
            'teacher_id.exists' => 'Teacher ID yang dimasukkan tidak valid.',
        ];
    }
}
