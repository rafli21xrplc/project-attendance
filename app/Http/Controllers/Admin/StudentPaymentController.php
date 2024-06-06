<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\StudentPaymentInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentPaymentController extends Controller
{

    private StudentPaymentInterface $studentPayment;

    public function __construct(StudentPaymentInterface $studentPayment)
    {
        $this->studentPayment = $studentPayment;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = $this->studentPayment->getTypeClassroom();
        $classroom = $this->studentPayment->getClassroom();
        $student = $this->studentPayment->getStudent();
        $payment = $this->studentPayment->getPayment();
        $studentPayment = $this->studentPayment->get();
        return view('admin.student_payment', compact('studentPayment', 'student', 'payment', 'classroom', 'type'));
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
