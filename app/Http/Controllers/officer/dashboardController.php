<?php

namespace App\Http\Controllers\officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    protected function index()
    {
        return view('officer.dashboard');
    }
}
