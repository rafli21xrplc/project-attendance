<?php

namespace App\Http\Requests\setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class settingRequest extends FormRequest
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
            'start-date' => 'required|date|before:end-date',
            'end-date' => 'required|date|after:start-date',
            'first-holiday' => ['required', Rule::in(['none', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])],
            'second-holiday' => ['required', Rule::in(['none', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'])]
        ];
    }

    public function messages()
    {
        return [
            'start-date.required' => 'Tanggal awal masa KBM harus diisi.',
            'start-date.date' => 'Tanggal awal masa KBM harus berupa tanggal yang valid.',
            'start-date.before' => 'Tanggal awal masa KBM harus sebelum tanggal akhir masa KBM.',
            'end-date.required' => 'Tanggal akhir masa KBM harus diisi.',
            'end-date.date' => 'Tanggal akhir masa KBM harus berupa tanggal yang valid.',
            'end-date.after' => 'Tanggal akhir masa KBM harus setelah tanggal awal masa KBM.',
            'first-holiday.required' => 'Libur mingguan pertama harus dipilih.',
            'first-holiday.in' => 'Libur mingguan pertama yang dipilih tidak valid.',
            'second-holiday.required' => 'Libur mingguan kedua harus dipilih.',
            'second-holiday.in' => 'Libur mingguan kedua yang dipilih tidak valid.'
        ];
    }
}
