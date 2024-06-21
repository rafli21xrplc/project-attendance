<?php

namespace App\Http\Requests\classroom;

use Illuminate\Foundation\Http\FormRequest;

class importRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'max:5120',
                'mimes:xlsx,xls',
            ],
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Harap pilih file Excel.',
            'file.file' => 'File yang diunggah harus berupa file Excel.',
            'file.max' => 'Ukuran file Excel maksimal 5 MB.',
            'file.mimes' => 'Format file Excel tidak valid. Format yang diizinkan: .xlsx atau .xls.',
        ];
    }
}
