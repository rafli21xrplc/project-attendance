<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\storeAttendanceTeacherHomeRoomRequest;
use App\Traits\TeacherTrait;
use Illuminate\Http\Request;

class attendanceSpesialDayController extends Controller
{

    use TeacherTrait;
    
    public function index()
    {
        $classroom = $this->getClassroomTeacher();
        $isDay = $this->checkIsSpesialDay();
        $isDayDone = $this->checkIsSpesialDayDone();
        return view('teacher.attendanceSpesialDay', compact('classroom', 'isDay', 'isDayDone'));
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
    public function store(storeAttendanceTeacherHomeRoomRequest $request, $id)
    {
        try {
            $request->validated();
            $attendances = $request->input('attendance');
            $this->storeAttendanceHomeRoom($attendances, $id);
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
