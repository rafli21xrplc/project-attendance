<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Traits\ScheduleTrait;

class DashboardController extends Controller
{
    use ScheduleTrait;

    public function index()
    {
        $schedule = $this->getClassroom();
        $typeClasses = $this->showClassrooms();
        $schedules = $this->showTeacherSchedule();
        $violations = $this->getTop10Violations();
        return view('teacher.dashboard', compact('typeClasses', 'schedule', 'schedules', 'violations'));
    }
}
