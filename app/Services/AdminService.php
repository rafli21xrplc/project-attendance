<?php

namespace App\Services;

use App\Http\Requests\Admin\StoreRequest;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ];
    }
}
