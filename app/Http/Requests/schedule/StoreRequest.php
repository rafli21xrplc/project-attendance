<?php

namespace App\Http\Requests\schedule;

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
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'teacher_id' => 'required|exists:teacher,id', // Teacher ID harus ada di tabel teachers
            'classroom_id' => 'required|exists:class_room,id', // Class ID harus ada di tabel classrooms
            'course_id' => 'required|exists:course,id', // Course ID harus ada di tabel courses
            'start_time_schedule_id' => 'required|exists:time_schedules,id', // Angka 0-23 untuk jam
            'end_time_schedule_id' => 'required|exists:time_schedules,id', // End time harus lebih besar dari start time
        ];
    }

    public function messages()
    {
        return [
            'day_of_week.required' => 'Hari harus dipilih.',
            'day_of_week.in' => 'Hari yang dipilih tidak valid.',
            'start_time_schedule_id.required' => 'Waktu mulai harus dipilih.',
            'end_time_schedule_id.required' => 'Waktu akhir harus dipilih.',
            'teacher_id.required' => 'Pengajar harus dipilih.',
            'teacher_id.exists' => 'Pengajar yang dipilih tidak valid.',
            'classroom_id.required' => 'Kelas harus dipilih.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak valid.',
            'course_id.required' => 'Pelajaran harus dipilih.',
            'course_id.exists' => 'Pelajaran yang dipilih tidak valid.',
            'start_time_schedule_id.exists' => 'Waktu yang dipilih tidak valid.',
            'end_time_schedule_id.exists' => 'Waktu yang dipilih tidak valid.',
        ];
    }
}
