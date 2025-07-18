<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Http\Requests\permission\permissionRequest;
use App\Models\schedule;
use App\Traits\StudentTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    use StudentTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qrCode = $this->checkPaymentStudent();
        $schedule = $this->getSchedule();
        return view('student.schedule', compact('schedule', 'qrCode'));
    }

    public function permission(permissionRequest $request)
    {
        try {
            $this->storePermission($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'letter failed to send.');
        }
        return redirect()->back()->with('success', 'letter was sent successfully.');
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
