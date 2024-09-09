<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\student;
use App\Models\student_payment;
use Illuminate\Http\Request;

class paymentController extends Controller
{

    public function getInstallment($id)
    {
        $student = student::where('user_id', $id)->first();

        $payments = student_payment::where('student_id', $student->id)
            ->with(['paymentInstallments.typePayment', 'paymentInstallments.payment'])
            ->get();

        $installments = $payments->flatMap(function ($payment) {
            return $payment->paymentInstallments->map(function ($installment) {
                $installment->payment = $installment->payment;
                return $installment;
            });
        });

        return $installments->values();
    }


    public function getPayment($id)
    {

        $student = student::where('user_id', $id)->first();

        $payments = student_payment::where('student_id', $student->id)->with(['payment', 'student.classroom.typeClass', 'paymentInstallments.typePayment'])->get();

        $installments = $payments->map(function ($payment) {
            return $payment->paymentInstallments;
        });

        return [
            'payments' => $payments,
            'total_payment' => $payments->count(),
            'total_instalment' => $installments->count(),
        ];
    }
}
