<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Traits\HistoryAttendanceTrait;
use Illuminate\Http\Request;

class HistoryAttendaceController extends Controller
{
    use HistoryAttendanceTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedule = $this->getScheduleClassroomHistory();
        return view('teacher.historyAttendance', compact('schedule'));
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
