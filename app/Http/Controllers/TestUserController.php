<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestUserController extends Controller
{
    public function index()
    {
        $users = [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
            ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com']
        ];
        // $users = \App\Models\User::all();
        return view('users.index', compact('users'));
    }
}
