<?php

namespace App\Http\Requests\attendance;

use Illuminate\Foundation\Http\FormRequest;

class attendanceRequest extends FormRequest
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
        $rules = [
            'attendance' => 'required|array',
        ];

        $attendances = $this->input('attendance', []);
        if (is_array($attendances)) {
            foreach ($attendances as $key => $value) {
                $rules["attendance.$key"] = 'required|in:present,alpha,permission,sick';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'attendance.required' => 'Attendance data is required.',
            'attendance.*.required' => 'All attendance statuses must be selected.',
            'attendance.*.in' => 'Invalid attendance status selected.',
        ];
    }
}
