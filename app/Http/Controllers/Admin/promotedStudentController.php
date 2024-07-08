<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\promotedStudent\updateRequest;
use App\Traits\PromotedTrait;
use Illuminate\Http\Request;

class promotedStudentController extends Controller
{

    use PromotedTrait;

    /**
     * Display a listing of the resource.
     */
    public function promoted()
    {
        $this->promotedStudentAll();
        try {
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }
    
    public function index()
    {
        $student = $this->get();
        $classroom = $this->getClassroom();
        return view('admin.promotedStudent', compact(['student', 'classroom']));
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
    public function update(updateRequest $request)
    {
        try {
            $this->promotedStudent($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
