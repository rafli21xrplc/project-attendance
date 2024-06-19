<?php

namespace App\Http\Requests\kbm_period;

use Illuminate\Foundation\Http\FormRequest;

class updateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Change this to your authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The schedule name is required.',
            'name.string' => 'The schedule name must be a string.',
            'name.max' => 'The schedule name must be no more than 255 characters.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date format (YYYY-MM-DD).',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date format (YYYY-MM-DD).',
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
