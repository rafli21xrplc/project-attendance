<?php

namespace App\Http\Requests\absence_point;

use Illuminate\Foundation\Http\FormRequest;

class storeRequest extends FormRequest
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
            'hours_absent' => 'required|integer',
            'points' => 'required|string', 
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
            'hours_absent.required' => 'The number of absent hours is required.',
            'hours_absent.integer' => 'The number of absent hours must be an integer.',
            'hours_absent.unique' => 'This number of absent hours has already been recorded.',
            'points.required' => 'The points deduction is required.',
            'points.numeric' => 'The points deduction must be a number.',
            'points.between' => 'The points deduction must be between 0 and 1.', // Adjust the error message based on your points system
        ];
    }
}
