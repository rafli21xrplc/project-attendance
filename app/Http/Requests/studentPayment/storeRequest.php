<?php

namespace App\Http\Requests\studentPayment;

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
            'payment_id' => 'required|exists:payment,id',
            'classroom_id' => 'required|exists:class_room,id',
            'selected_student_ids' => 'required|array',
            'selected_student_ids.*' => 'exists:student,id',
        ];
    }

    public function messages()
    {
        return [
            'payment_id.required' => 'Kolom Pembayaran wajib diisi.',
            'payment_id.exists' => 'Pembayaran yang dipilih tidak valid.',
            'classroom_id.required' => 'Kolom Kelas wajib diisi.',
            'classroom_id.exists' => 'Kelas yang dipilih tidak valid.',
            'selected_student_ids.required' => 'Anda harus memilih setidaknya satu siswa.',
            'selected_student_ids.array' => 'Format data siswa tidak valid.',
            'selected_student_ids.*.exists' => 'Siswa yang dipilih tidak valid.',
        ];
    }
}
