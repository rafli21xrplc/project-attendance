<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\ScheduleInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\schedule\StoreRequest;
use App\Http\Requests\schedule\UpdateRequest;
use App\Models\schedule;
use Illuminate\Http\Request;

class scheduleController extends Controller
{

    private ScheduleInterface $schedule;

    public function __construct(ScheduleInterface $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = $this->schedule->get();
        $classroom = $this->schedule->getClassroom();
        $course = $this->schedule->getCourse();
        $teacher = $this->schedule->getTeacher();
        $time_schedule = $this->schedule->getTimeSchedule();
        return view('admin.schedule', compact('schedule', 'classroom', 'course', 'teacher', 'time_schedule'));
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
    public function store(StoreRequest $request)
    {
        try {
            $this->schedule->store($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed created');
        }
        return redirect()->back()->with('success', 'success created');
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
    public function update(UpdateRequest $request, schedule $schedule)
    {
        $this->schedule->update($schedule->id, $request->validated());
        try {
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(schedule $schedule)
    {
        try {
            $this->schedule->delete($schedule->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
