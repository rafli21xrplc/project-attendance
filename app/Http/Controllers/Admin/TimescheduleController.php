<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\TimeScheduleInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\timeSchedule\StoreRequest;
use App\Http\Requests\timeSchedule\UpdateRequest;
use App\Models\time_schedule;
use Illuminate\Http\Request;

class TimescheduleController extends Controller
{
    private TimeScheduleInterface $timeScheduleInterface;
    public function __construct(TimeScheduleInterface $timeScheduleInterface)
    {
        $this->timeScheduleInterface = $timeScheduleInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $time = $this->timeScheduleInterface->get();
        return view('admin.time_schedule', compact('time'));
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
        $this->timeScheduleInterface->store($request->validated());
        try {
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
    public function update(UpdateRequest $request, time_schedule $time_schedule)
    {
        try {
            $this->timeScheduleInterface->update($time_schedule->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(time_schedule $time_schedule)
    {
        try {
            $this->timeScheduleInterface->delete($time_schedule->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
