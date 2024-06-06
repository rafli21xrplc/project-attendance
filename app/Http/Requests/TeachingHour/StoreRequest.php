<?php

namespace App\Http\Requests\TeachingHour;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'classroom_id' => 'required|exists:class_room,id',
            'course_id' => 'required|exists:course,id',
            'teacher_id' => 'required|exists:teacher,id',
            'hour' => 'required|integer|min:1|max:24',
        ];
    }

    public function messages()
    {
        return [
            'classroom_id.required' => 'classroom ID harus diisi.',
            'classroom_id.exists' => 'classroom ID yang dimasukkan tidak valid.',
            'course_id.required' => 'Course ID harus diisi.',
            'course_id.exists' => 'Course ID yang dimasukkan tidak valid.',
            'teacher_id.required' => 'Teacher ID harus diisi.',
            'teacher_id.exists' => 'Teacher ID yang dimasukkan tidak valid.',
            'hour.required' => 'Jam harus diisi.',
            'hour.integer' => 'Jam harus berupa angka.',
            'hour.min' => 'Jam harus lebih besar dari 0.',
            'hour.max' => 'Jam tidak boleh lebih dari 24.',
        ];
    }
}
