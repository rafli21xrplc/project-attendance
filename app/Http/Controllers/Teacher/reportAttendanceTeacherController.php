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

class reportAttendanceTeacherController extends Controller
{
    use HistoryAttendanceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacherId = Auth::user()->teacher->id;
        $schedule = schedule::with(['course', 'classroom.students', 'classroom.typeClass', 'StartTimeSchedules', 'EndTimeSchedules'])->where('teacher_id', $teacherId)  
            ->get();
        $courses = $this->getCourseTeacher();
        $classrooms = $this->getClassroomTeacher();
        return view('teacher.reportTeacherAttendance', compact('schedule', 'courses', 'classrooms'));
    }

    public function search(SearchAttendanceStudentRequest $request)
    {
        $date = Carbon::parse($request->date);

        $attendances = attendance::whereDate('created_at', $date)
            ->with(['student', 'schedule.course', 'schedule.classroom.students', 'schedule.classroom.typeClass', 'schedule.StartTimeSchedules', 'schedule.EndTimeSchedules'])
            ->get()->unique('schedule_id');
        $courses = $this->getCourseTeacher();
        $classrooms = $this->getClassroomTeacher();
    
        return view('teacher.historyAttendance', compact('attendances', 'courses', 'classrooms'));
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
