<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\ClassroomTeacherInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClassroomTeacher\StoreRequest;
use App\Http\Requests\ClassroomTeacher\UpdateRequest;
use App\Models\classroom_teacher;
use Illuminate\Http\Request;

class classroomTeacherController extends Controller
{
    private ClassroomTeacherInterface $classroomTeacher;

    public function __construct(ClassroomTeacherInterface $classroomTeacher)
    {
        $this->classroomTeacher = $classroomTeacher;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classroom = $this->classroomTeacher->getClassroom();
        $teacher = $this->classroomTeacher->getTeacher();
        $classroomTeacher = $this->classroomTeacher->get();
        return view('admin.classroomTeacher', compact('classroomTeacher', 'classroom', 'teacher'));
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
            $this->classroomTeacher->store($request->validated());
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
    public function update(UpdateRequest $request, classroom_teacher $classroom_teacher)
    {
        try {
            $this->classroomTeacher->update($classroom_teacher->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(classroom_teacher $classroom_teacher)
    {
        try {
            $this->classroomTeacher->delete($classroom_teacher->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
