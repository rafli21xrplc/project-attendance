<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\ExamLoginInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\examLogin\importRequest;
use App\Http\Requests\examLogin\storeRequest;
use App\Http\Requests\examLogin\updateRequest;
use App\Models\ExamLogin;
use App\Traits\ExamLoginTrait;
use Illuminate\Http\Request;

class ExamLoginController extends Controller
{
    use ExamLoginTrait;

    private ExamLoginInterface $ExamLogin;

    public function __construct(ExamLoginInterface $ExamLogin)
    {
        $this->ExamLogin = $ExamLogin;
    }

    public function import(importRequest $request)
    {
        try {
            $this->importTeachers($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed created');
        }
        return redirect()->back()->with('success', 'success created');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exam = $this->ExamLogin->get();
        $student = $this->ExamLogin->getStudent();
        return view('admin.exam_login', compact('exam', 'student'));
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
    public function store(storeRequest $request)
    {
        try {
            $this->ExamLogin->store($request->validated());
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
    public function update(updateRequest $request, $ExamLogin)
    {
        $this->ExamLogin->update($ExamLogin, $request->validated());
        try {
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ExamLogin)
    {

        try {
            $this->ExamLogin->delete($ExamLogin);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
