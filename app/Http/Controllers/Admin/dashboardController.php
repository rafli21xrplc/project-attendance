<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\classRoom;
use App\Models\course;
use App\Models\student;
use App\Models\teacher;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    protected function index()
    {
        $student = student::count();
        $teacher = teacher::count();
        $classroom = classRoom::count();
        $source = course::count();
        return view('admin.dashboard', compact('student', 'teacher', 'classroom', 'source'));
    }
}
