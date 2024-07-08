<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\ExamLogin;
use App\Models\student_payment;
use App\Traits\StudentTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class studentDashboardController extends Controller
{

    use StudentTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qrCode = $this->checkPaymentStudent();
        $schedules = $this->getScheduleStudent();
        return view('student.dashboard', compact('schedules', 'qrCode'));
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
