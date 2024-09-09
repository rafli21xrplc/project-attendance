<?php

namespace App\Http\Requests\timeSchedule;

use App\Rules\ValidTime;
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
            'time_number' => 'required|integer|min:1|max:10',
            'start_time_schedule' => ['required', new ValidTime()],
            'end_time_schedule' => ['required', 'after:start_time_schedule', new ValidTime()],
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
            'time_number.integer' => 'Kolom time_number harus berupa angka bulat.',
            'time_number.required' => 'Kolom time_number wajib diisi.',
            'start_time_schedule.required' => 'Kolom start_time_schedule wajib diisi.',
            'start_time_schedule.date_format' => 'Format start_time_schedule harus HH:MM:SS.',
            'end_time_schedule.required' => 'Kolom end_time_schedule wajib diisi.',
            'end_time_schedule.date_format' => 'Format end_time_schedule harus HH:MM:SS.',
            'end_time_schedule.after' => 'Kolom end_time_schedule harus lebih besar dari start_time_schedule.'
        ];
    }
}
