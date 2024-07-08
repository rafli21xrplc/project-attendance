<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendanceRequest;
use App\Models\schedule;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Request;

class AttendanceStudentController extends Controller
{

    use AttendanceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendance = $this->getAttendance();
        $classroom = $this->getClassrooms();
        $schedules = null;

        return view('admin.attendance_student', compact('attendance', 'classroom', 'schedules'));
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
        try {
            $this->updateAttendanceStudent($request->all(), $id);
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

    public function search(SearchAttendanceRequest $request)
    {

        $classroom = $this->getClassrooms();
        // $schedules = $this->getSchedules($request->validated());
        $schedules = schedule::getSchedules($request->validated());

        $attendanceBySchedule = [];

        foreach ($schedules as $schedule) {
            foreach ($schedule->classroom->students as $student) {
                if (!isset($attendanceBySchedule[$schedule->id][$student->id])) {
                    $attendanceBySchedule[$schedule->id][$student->id] = [
                        'student_id' => $student->id,
                        'name' => $student->name,
                        'attendance_status' => [],
                    ];
                }

                $attendance = $schedule->attendances->firstWhere('student_id', $student->id);

                $attendanceBySchedule[$schedule->id][$student->id]['attendance_status'][] = $attendance ? $attendance->status : null;
            }
        }

        return view('admin.attendance_student', compact('schedules', 'classroom', 'attendanceBySchedule'));
    }

    public function showResults()
    {
        if (session()->has('search_results')) {
            $schedules = session('schedules');
            $classroom = session('classroom');
            $attendanceBySchedule = session('attendanceBySchedule');
            return view('admin.attendance_student', compact('schedules', 'classroom', 'attendanceBySchedule'));
        }

        return redirect()->back()->with('error', 'No search results found.');
    }
}
