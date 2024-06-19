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
            'range-date' => ['required', 'regex:/^\d{4}-\d{2}-\d{2}\sto\s\d{4}-\d{2}-\d{2}$/'],
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
