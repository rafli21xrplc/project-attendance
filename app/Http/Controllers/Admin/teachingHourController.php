<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\TeachingHourInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeachingHour\StoreRequest;
use App\Http\Requests\TeachingHour\UpdateRequest;
use App\Models\teaching_hour;
use Illuminate\Http\Request;

class teachingHourController extends Controller
{
    private TeachingHourInterface $teachingHour;
    /**
     * Display a listing of the resource.
     */
    public function __construct(TeachingHourInterface $teachingHour)
    {
        $this->teachingHour = $teachingHour;
    }


    public function index()
    {
        $teaching = $this->teachingHour->get();
        $teacher = $this->teachingHour->getTeacher();
        $course = $this->teachingHour->getCourse();
        $classroom = $this->teachingHour->getClassroom();
        return view('admin.teaching_hour', compact('teaching', 'course', 'teacher', 'classroom'));
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
            $this->teachingHour->store($request->validated());
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
    public function update(UpdateRequest $request, teaching_hour $teaching_hour)
    {
        try {
            $this->teachingHour->update($teaching_hour->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(teaching_hour $teaching_hour)
    {
        try {
            $this->teachingHour->delete($teaching_hour->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
