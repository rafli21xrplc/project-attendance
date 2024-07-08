<?php

namespace App\Services;

use App\Models\PaymentInstallment;
use App\Models\student_payment;

class PaymentService
{
    public function checkingPayment(array $data)
    {
        $student_payment = student_payment::with(['payment', 'student'])->findOrFail($data['student_payment_id']);
    
        $payment_installments = PaymentInstallment::where('student_payment_id', $data['student_payment_id'])->get();
    
        $total_installments = $payment_installments->sum('amount');
    
        if ($total_installments >= $student_payment->payment->amount) {
            $student_payment->is_paid = true;
            $student_payment->save();
            return true;
        }
    
        return false;
    }
    
}
