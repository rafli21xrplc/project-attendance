<?php

namespace App\Http\Requests\attendance;

use Illuminate\Foundation\Http\FormRequest;

class SearchAttendanceStudentRequest extends FormRequest
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
            'date' => ['required', 'date', 'before_or_equal:today'], 
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
            'date.required' => 'Tanggal harus diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'date.before_or_equal' => 'Tanggal harus sama dengan atau sebelum hari ini.',
            'classroom_id.required' => 'Kelas harus dipilih.',
            'classroom_id.integer' => 'Kelas tidak valid.',
            'classroom_id.exists' => 'Kelas tidak ditemukan.',
            'course_id.required' => 'Mata pelajaran harus dipilih.',
            'course_id.integer' => 'Mata pelajaran tidak valid.',
            'course_id.exists' => 'Mata pelajaran tidak ditemukan.',
        ];
    }
}
