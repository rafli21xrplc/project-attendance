<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Models\attendance;
use App\Models\classRoom;
use App\Models\schedule;
use App\Traits\HistoryAttendanceTrait;
use Illuminate\Http\Request;

class HistoryAttendaceStudentController extends Controller
{
    use HistoryAttendanceTrait;

    public function index(string $classroom_id, string $schedule_id)
    {
        $attendance = $this->getAttendaence($classroom_id, $schedule_id);
        return view('teacher.historyAttendanceStudent')->with([
            'classroom' => $attendance['classroom'],
            'schedule' => $attendance['schedule'],
            'student' => $attendance['student'],
            'attendanceData' => $attendance['attendanceData'],
        ]);
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
