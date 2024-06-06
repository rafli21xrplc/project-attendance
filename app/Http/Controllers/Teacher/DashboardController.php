<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\type_class;
use App\Models\User;
use App\Traits\ScheduleTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ScheduleTrait;

    public function index()
    {
        $typeClasses = type_class::with('classRooms')->get();
        $schedule = $this->getClassroom();
        return view('teacher.dashboard', compact('typeClasses', 'schedule'));
    }
}
