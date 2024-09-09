<?php

namespace App\Http\Controllers\Teacher;

use App\Contracts\Interfaces\ShowAttendanceStudentInterface;
use App\Contracts\Interfaces\Teacher\StudentAttendanceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\attendanceRequest;
use App\Http\Requests\attendance\SearchAttendanceStudentRequest;
use App\Models\attendance;
use App\Models\schedule;
use App\Models\student;
use App\Models\teacher;
use App\Traits\AttendanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendaceStudentController extends Controller
{

    use AttendanceTrait;

    private StudentAttendanceInterface $StudentAttendanceInterface;

    public function __construct(StudentAttendanceInterface $StudentAttendanceInterface)
    {
        $this->StudentAttendanceInterface = $StudentAttendanceInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(string $classroom_id, string $schedule_id)
    {
        $schedule = $this->StudentAttendanceInterface->getSchedule($schedule_id);
        $student = $this->StudentAttendanceInterface->getAttendance($classroom_id);
        $classroom = $this->StudentAttendanceInterface->getClassroomById($classroom_id);
        $attendance = $this->StudentAttendanceInterface->storeAttendance($student, $schedule->id);
        return view('teacher.attendance_student', compact('student', 'classroom', 'schedule', 'attendance'));
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
            $attendances = $request->input('attendance');
            $this->StudentAttendanceInterface->storeAttendance($attendances, $id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed attendance');
        }
        return redirect()->route('teacher.attendance_teacher.index')->with('success', 'success attendance');
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
    public function update(Request $request)
    {
        try {
            $this->updateAttendanceStudent($request->all());
        } catch (\Throwable $th) {
            return redirect()->route('admin.attendance.results')->with('error', 'Attendance updated successfully.');
        }
        return redirect()->route('admin.attendance.results')->with('success', 'Attendance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
