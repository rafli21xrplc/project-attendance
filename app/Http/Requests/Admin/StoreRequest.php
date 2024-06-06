<?php

namespace App\Http\Requests\Admin;

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
            'name' => 'required|string|max:255', // Nama harus diisi, berupa string, dan maksimal 255 karakter
            'email' => 'required|email|unique:users,email|max:255', // Email harus diisi, berupa email yang valid, unik dalam tabel users, dan maksimal 255 karakter
            'password' => 'required|string|min:8|max:255', // Password harus diisi, berupa string dengan panjang minimal 8 karakter dan maksimal 255 karakter
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari :max karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email harus berupa format email yang valid.',
            'email.unique' => 'Email sudah digunakan.',
            'email.max' => 'Email tidak boleh lebih dari :max karakter.',
            'password.required' => 'Password harus diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal harus :min karakter.',
            'password.max' => 'Password tidak boleh lebih dari :max karakter.',
        ];
    }
}
