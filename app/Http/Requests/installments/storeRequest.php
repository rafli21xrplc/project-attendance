<?php

namespace App\Http\Requests\installments;

use Illuminate\Foundation\Http\FormRequest;

class storeRequest extends FormRequest
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
            'student_payment_id' => 'required|exists:student_payment,id',
            'amount' => 'required|numeric|min:0',
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
            'student_payment_id.required' => 'Kolom Tagihan Siswa wajib diisi.',
            'student_payment_id.exists' => 'Tagihan Siswa tidak valid.',
            'amount.required' => 'Kolom Jumlah wajib diisi.',
            'amount.numeric' => 'Kolom Jumlah harus berupa angka.',
            'amount.min' => 'Kolom Jumlah harus lebih besar atau sama dengan 0.',
        ];
    }
}
