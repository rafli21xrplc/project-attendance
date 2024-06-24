<?php

namespace App\Http\Requests\TypeClass;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'category' => 'required|string|min:3|max:255|unique:type_class,category', // Aturan validasi yang ketat
        ];
    }

    public function messages()
    {
        return [
            'category.required' => 'Nama pelajaran diperlukan.',
            'category.string' => 'Nama pelajaran harus berupa teks.',
            'category.min' => 'Nama pelajaran harus memiliki minimal 3 karakter.',
            'category.max' => 'Nama pelajaran tidak boleh lebih dari 255 karakter.',
            'category.unique' => 'Nama pelajaran sudah digunakan, silakan pilih nama lain.',
        ];
    }
}
