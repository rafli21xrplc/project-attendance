<?php

namespace App\Http\Controllers\Teacher;

use App\Contracts\Interfaces\Teacher\AttendanceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\attendanceRequest;
use App\Traits\TeacherTrait;
use Illuminate\Http\Request;

class AttendaceController extends Controller
{
    use TeacherTrait;
    private AttendanceInterface $attendanceInterface;

    public function __construct(AttendanceInterface $attendanceInterface)
    {
        $this->attendanceInterface = $attendanceInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = $this->attendanceInterface->getSchedule();
        return view('teacher.attendance', compact('schedule'));
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
    public function store(attendanceRequest $request, $id)
    {
        try {
            $request->validated();
            $attendances = $request->input('attendance');
            $this->storeAttendanceStudent($attendances, $id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed attendance');
        }
        return redirect()->back()->with('success', 'success attendance');
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
