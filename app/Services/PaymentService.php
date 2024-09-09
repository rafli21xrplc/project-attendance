<?php

namespace App\Services;

use App\Models\payment;
use App\Models\PaymentInstallment;
use App\Models\student_payment;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentService
{
    public function checkingPayment($data)
    {
        $student_payment = student_payment::with(['payment', 'student'])->findOrFail($data['student_payment_id']);

        $payment_installments = PaymentInstallment::where('student_payment_id', $data['student_payment_id'])->get();

        $total_installments = $payment_installments->sum('amount');

        if ($total_installments >= $student_payment->payment->amount) {
            $student_payment->is_paid = true;
            $student_payment->save();
            return true;
        } else {
            $student_payment->is_paid = false;
            $student_payment->save();
            return true;
        }
    }

    public function store(array $data)
    {
        $installmentData = [
            'id' => Str::uuid(),
            'student_payment_id' => $data['student_payment_id'],
            'amount' => $data['amount'],
            'payment_date' => Carbon::now(),
            'type_payment_id' => $data['type_payment_id'],
            'description' => $data['description'] ?? null,
        ];

        PaymentInstallment::create($installmentData);
    }
}
