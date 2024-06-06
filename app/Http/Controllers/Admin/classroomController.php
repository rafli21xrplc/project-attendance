<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\ClassInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classroom\StoreRequest;
use App\Http\Requests\Classroom\UpdateRequest;
use App\Models\classRoom;
use Illuminate\Http\Request;

class classroomController extends Controller
{
    private ClassInterface $class_room;

    /**
     * Display a listing of the resource.
     */
    public function __construct(ClassInterface $class_room)
    {
        $this->class_room = $class_room;
    }

    
    public function index()
    {
        $type_class = $this->class_room->getTypeClassroom();
        $classroom = $this->class_room->get();
        $teacher = $this->class_room->getTeacher();
        return view('admin.classroom', compact('classroom', 'type_class', 'teacher'));
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
            $this->class_room->store($request->validated());
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
    public function update(UpdateRequest $request, classRoom $class_room)
    {
        try {
            $this->class_room->update($class_room->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(classRoom $class_room)
    {
        try {
            $this->class_room->delete($class_room->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
