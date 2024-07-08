<?php

namespace App\Http\Requests\studentPayment;

use Illuminate\Foundation\Http\FormRequest;

class updateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_paid' => $this->has('is_paid'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'student_id' => 'required|exists:student,id',
            'payment_id' => 'required|exists:payment,id',
            'payment_date' => 'required|date',
            'is_paid' => 'required|boolean',
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
            'student_id.required' => 'The student field is required.',
            'student_id.exists' => 'The selected student is invalid.',
            'payment_id.required' => 'The payment field is required.',
            'payment_id.exists' => 'The selected payment is invalid.',
            'payment_date.required' => 'The payment date field is required.',
            'payment_date.date' => 'The payment date is not a valid date.',
            'is_paid.required' => 'The payment status field is required.',
            'is_paid.boolean' => 'The payment status field must be true or false.',
        ];
    }
}
