<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\StudentPaymentInterface;
use App\Exports\studentPaymentMonthExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\studentPayment\storeRequest;
use App\Http\Requests\studentPayment\updateRequest;
use App\Models\classRoom;
use App\Models\student;
use App\Models\student_payment;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentPaymentController extends Controller
{
    use PaymentTrait;

    private StudentPaymentInterface $studentPayment;

    public function __construct(StudentPaymentInterface $studentPayment)
    {
        $this->studentPayment = $studentPayment;
    }

    public function export(Request $request)
    {
        $month = $request->input('month');

        $formattedMonth = Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F');

        $classrooms = classRoom::with(['students.studentPayments.payment', 'students.studentPayments.paymentInstallments', 'typeClass', 'teacher'])->get();

        return Excel::download(new studentPaymentMonthExport($classrooms, $formattedMonth), 'student_payment_report.xlsx');
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
        $typePayment = $this->studentPayment->getTypePayment();

        return view('admin.student_payment', compact('studentPayment', 'student', 'payment', 'classroom', 'type', 'typePayment'));
    }

    public function getStudentsByClassroom(Request $request)
    {
        $classroomId = $request->query('classroom_id');
        $students = student::where('classroom_id', $classroomId)->get();
        return response()->json($students);
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
            $this->storeStudentPayment($request->validated());
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
    public function update(updateRequest $request, student_payment $studentPayment)
    {
        $this->studentPayment->update($studentPayment->id, $request->validated());
        try {
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(student_payment $studentPayment)
    {
        try {
            $this->studentPayment->delete($studentPayment->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}