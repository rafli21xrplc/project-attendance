<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\type_class;
use App\Models\User;
use App\Traits\ScheduleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
