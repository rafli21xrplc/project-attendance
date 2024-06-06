<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\TeacherInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreRequest as storeTeacher;
use App\Http\Requests\Teacher\UpdateRequest as updateTeacher;
use App\Http\Requests\teacherRequest;
use App\Models\religion;
use App\Models\teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class teacherController extends Controller
{
    private TeacherInterface $teacher;

    public function __construct(TeacherInterface $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacher = $this->teacher->get();
        $religi = $this->teacher->getReligi();
        return view('admin.teacher', compact('teacher', 'religi'));
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
    public function store(storeTeacher $request): RedirectResponse
    {
        try {
            $this->teacher->store($request->validated());
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
    public function update(updateTeacher $request, teacher $teacher)
    {
        try {
            $this->teacher->update($teacher->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(teacher $teacher)
    {
        try {
            $this->teacher->delete($teacher->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
