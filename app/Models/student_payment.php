<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class student_payment extends Model
{
    use HasFactory;

    protected $table = 'student_payment';
    protected $fillable = [
        'id',
        'student_id',
        'payment_id',
        'is_paid',
        'payment_date'
    ];
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function student()
    {
        return $this->belongsTo(student::class, 'student_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(payment::class, 'payment_id', 'id');
    }

    public static function getStudentPayments()
    {
        $studentPayments = DB::table('student_payment')
            ->select(
                'student_payment.*',
                'student.name as student_name',
                'payment.name as payment_name',
                'payment.amount as payment_amount'
            )
            ->join('student', 'student_payment.student_id', '=', 'student.id')
            ->join('payment', 'student_payment.payment_id', '=', 'payment.id')
            ->get();

        $studentPaymentIds = $studentPayments->pluck('id');

        $paymentInstallments = DB::table('payment_installment')
            ->whereIn('student_payment_id', $studentPaymentIds)
            ->get()
            ->groupBy('student_payment_id');

        $studentPayments = $studentPayments->map(function ($payment) use ($paymentInstallments) {
            $payment->installments = $paymentInstallments->get($payment->id, collect())->toArray();
            return $payment;
        });

        return $studentPayments;
    }

    public static function getStudentPaymentsInstallments()
    {
        $studentPayments = DB::table('student_payment')
            ->select(
                'student_payment.*',
                'student.name as student_name',
                'payment.name as payment_name',
                'payment.amount as payment_amount'
            )
            ->join('student', 'student_payment.student_id', '=', 'student.id')
            ->join('payment', 'student_payment.payment_id', '=', 'payment.id')
            ->where('student_payment.is_paid', false)
            ->get();

        $studentPaymentIds = $studentPayments->pluck('id');

        $paymentInstallments = DB::table('payment_installment')
            ->whereIn('student_payment_id', $studentPaymentIds)
            ->get()
            ->groupBy('student_payment_id');

        $studentPayments = $studentPayments->map(function ($payment) use ($paymentInstallments) {
            $payment->installments = $paymentInstallments->get($payment->id, collect())->toArray();
            return $payment;
        });

        return $studentPayments;
    }
}
