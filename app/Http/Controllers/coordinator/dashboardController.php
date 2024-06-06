<?php

namespace App\Http\Controllers\coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    protected function index()
    {
        return view('coordinator.dashboard');
    }
}
