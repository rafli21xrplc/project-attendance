<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Traits\ScheduleTrait;

class DashboardController extends Controller
{
    use ScheduleTrait;
    public function index()
    {
        $schedules = $this->showTeacherSchedule();
        return view('teacher.dashboard', compact('schedules'));
    }
}
