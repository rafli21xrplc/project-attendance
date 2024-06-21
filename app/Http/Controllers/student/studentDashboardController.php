<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Traits\StudentTrait;
use Illuminate\Http\Request;

class studentDashboardController extends Controller
{

    use StudentTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = $this->getScheduleStudent();
        return view('student.dashboard', compact('schedules'));
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
