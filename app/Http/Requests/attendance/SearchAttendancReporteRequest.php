<?php

namespace App\Http\Requests\attendance;

use Illuminate\Foundation\Http\FormRequest;

class SearchAttendancReporteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Replace with your authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start-date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:end-date'],
            'end-date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:start-date'],
            'states' => ['required', 'array'],
            'states.*' => ['uuid']
        ];
    }


    public function messages()
    {
        return [
            'range-date.required' => 'Field range-date wajib diisi.',
            'range-date.regex' => 'Format range-date harus berupa "YYYY-MM-DD to YYYY-MM-DD".',
            'states.required' => 'Field classroom_id wajib diisi.',
            'states.uuid' => 'Field classroom_id harus berupa UUID yang valid.'
        ];
    }
}
