<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\StudentInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\student\importRequest;
use App\Http\Requests\Student\StoreRequest;
use App\Http\Requests\Student\UpdateRequest;
use App\Models\student;
use App\Models\User;
use App\Traits\StudentTrait;
use Illuminate\Http\Request;

class studentController extends Controller
{
    use StudentTrait;

    private StudentInterface $student;

    public function __construct(StudentInterface $student)
    {
        $this->student = $student;
    }

    /**
     * Display a listing of the resource.
     */

    public function search(Request $request)
    {
        $student = $this->searchStudent($request->classroom_id);
        $type_class = $this->student->getTypeClassroom();
        $religi = $this->student->getReligi();
        $class_room = $this->student->getClassroom();
        return view('admin.student', compact('student', 'religi', 'class_room', 'type_class'));
    }

    public function import(importRequest $request)
    {
        try {
            $this->importStudents($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed created');
        }
        return redirect()->back()->with('success', 'success created');
    }

    public function index()
    {
        $student = $this->student->get();
        $type_class = $this->student->getTypeClassroom();
        $religi = $this->student->getReligi();
        $class_room = $this->student->getClassroom();
        return view('admin.student', compact('student', 'religi', 'class_room', 'type_class'));
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
        $this->student->store($request->validated());
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
    public function update(UpdateRequest $request, student $student)
    {
        try {
            $this->student->update($student->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(student $student)
    {
        try {
            $this->student->delete($student->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
