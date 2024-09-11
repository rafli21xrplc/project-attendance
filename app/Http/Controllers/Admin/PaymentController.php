<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Interfaces\PaymentInterface;
use App\Exports\paymentInstallmentExport;
use App\Exports\studentPaymentAdditionExport;
use App\Exports\studentPaymentExport;
use App\Exports\studentPaymentMainExport;
use App\Exports\studentPaymentMonthExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\payment\importRequest;
use App\Http\Requests\Payment\StoreRequest;
use App\Http\Requests\Payment\UpdateRequest;
use App\Models\classRoom;
use App\Models\payment;
use App\Traits\PaymentTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{

    use PaymentTrait;

    private PaymentInterface $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function index()
    {
        $payment = $this->payment->get();
        $typePayment = $this->getTypePayments();
        return view('admin.payment', compact('payment', 'typePayment'));
    }

    public function import(importRequest $request)
    {
        try {
            $this->importPayment($request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed created');
        }
        return redirect()->back()->with('success', 'success created');
    }

    public function export($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->category == 'utama') {
            $classrooms = classRoom::with(['students.studentPayments' => function ($query) use ($id) {
                $query->where('payment_id', $id);
            }])->get();
            $month = strtolower(Carbon::now()->format('F'));

            
            return Excel::download(new studentPaymentMainExport($classrooms, $payment, $month), 'rekap_tunggakan.xlsx');
        } else {
            $classrooms = classRoom::with(['students.studentPayments' => function ($query) use ($id) {
                $query->where('payment_id', $id);
            }])->get();
            $month = strtolower(Carbon::now()->format('F'));

            return Excel::download(new studentPaymentAdditionExport($classrooms, $payment, $month), 'rekap_tunggakan.xlsx');
        }
    }

    public function exportRekapitulasi()
    {
        $classrooms = classRoom::with(['students.studentPayments'])->get();
        $month = strtolower(Carbon::now()->format('F'));
        return Excel::download(new studentPaymentMonthExport($classrooms, $month), 'rekap_tunggakan.xlsx');
    }

    public function exportInstallment(Request $request)
    {
        $payment = payment::findOrFail($request->payment);
        $classrooms = classRoom::with(['students.studentPayments'])->get();

        return Excel::download(new paymentInstallmentExport($classrooms, $payment), 'rekap_tunggakan.xlsx');
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
    // public function store(StoreRequest $request)
    public function store(StoreRequest $request)
    {
        try {
            $this->payment->store($request->validated());
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
    public function update(UpdateRequest $request, payment $payment)
    {
        try {
            $this->payment->update($payment->id, $request->validated());
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed update');
        }
        return redirect()->back()->with('success', 'success update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(payment $payment)
    {
        try {
            $this->payment->delete($payment->id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'failed delete');
        }
        return redirect()->back()->with('success', 'success delete');
    }
}
