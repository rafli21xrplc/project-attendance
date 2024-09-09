<?php

namespace App\Http\Requests\subtraction;

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
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'start_time' => 'required|numeric|min:1',
            'end_time' => 'required|numeric|min:2|gt:start_time',
        ];
    }

    public function messages()
    {
        return [
            'tanggal.required' => 'Tanggal libur mingguan pertama harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'start_time.required' => 'Jam awal harus diisi.',
            'start_time.numeric' => 'Jam awal harus berupa angka.',
            'start_time.min' => 'Jam awal minimal adalah 1.',
            'end_time.required' => 'Jam akhir harus diisi.',
            'end_time.numeric' => 'Jam akhir harus berupa angka.',
            'end_time.min' => 'Jam akhir minimal adalah 2.',
            'end_time.gt' => 'Jam akhir harus lebih besar dari jam awal.',
        ];
    }
}
