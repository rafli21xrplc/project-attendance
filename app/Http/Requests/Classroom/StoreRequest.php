<?php

namespace App\Http\Requests\Classroom;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'teacher_id' => 'required|exists:teacher,id',
            'type_class_id' => 'required|exists:type_class,id',
            'name' => 'required|string|min:3|max:255', // Aturan validasi yang ketat
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pelajaran diperlukan.',
            'name.string' => 'Nama pelajaran harus berupa teks.',
            'name.min' => 'Nama pelajaran harus memiliki minimal 3 karakter.',
            'name.max' => 'Nama pelajaran tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama pelajaran sudah digunakan, silakan pilih nama lain.',
            'teacher_id.required' => 'guru id harus diisi.',
            'teacher_id.exists' => 'guru id yang dimasukkan tidak valid.',
            'type_class_id.required' => 'type class id harus diisi.',
            'type_class_id.exists' => 'type class id yang dimasukkan tidak valid.',
        ];
    }
}
