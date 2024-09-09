<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendanceStudentRequest;
use App\Models\attendance;
use App\Models\schedule;
use App\Traits\HistoryAttendanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryAttendaceController extends Controller
{
    use HistoryAttendanceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = $this->getScheduleClassroomHistory();
        $courses = $this->getCourseTeacher();
        $classrooms = $this->getClassroomTeacher();
        return view('teacher.historyAttendance', compact('schedule', 'courses', 'classrooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
}
