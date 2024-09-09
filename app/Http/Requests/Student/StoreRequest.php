<?php

namespace App\Http\Requests\Student;

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
            'name' => 'required|string|max:255|min:2',
            'gender' => 'required|in:L,P',
            'day_of_birth' => 'required|date',
            'telp' => 'required|string|max:20|min:11',
            'classroom_id' => 'required',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.min' => 'Name must be at least :min characters',
            'name.max' => 'Name cannot exceed :max characters',
            'gender.required' => 'Gender is required',
            'gender.in' => 'Gender must be either pria or wanita',
            'address.required' => 'Alamat is required',
            'address.string' => 'Alamat must be a string',
            'address.min' => 'Alamat must be at least :min characters',
            'address.max' => 'Alamat cannot exceed :max characters',
            'born_at.required' => 'Born at is required',
            'born_at.string' => 'Born at must be a string',
            'born_at.min' => 'Born at must be at least :min characters',
            'born_at.max' => 'Born at cannot exceed :max characters',
            'day_of_birth.required' => 'Tanggal lahir is required',
            'day_of_birth.date' => 'Tanggal lahir must be a valid date',
            'telp.required' => 'Telepon is required',
            'telp.string' => 'Telepon must be a string',
            'telp.min' => 'Telepon must be at least :min characters',
            'telp.max' => 'Telepon cannot exceed :max characters',
            'classroom_id.required' => 'Kelas harus dipilih',
            'religion_id.required' => 'Agama harus dipilih',
            'username.required' => 'username is required',
            'username.unique' => 'username is already taken',
            'username.max' => 'username cannot exceed :max characters',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',   
            'password.min' => 'Password must be at least :min characters',
        ];
    }
}
