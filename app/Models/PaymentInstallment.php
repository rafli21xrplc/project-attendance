<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentInstallment extends Model
{
    use HasFactory;
    protected $table = 'payment_installment';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_payment_id',
        'amount',
        'payment_date',
        'type_payment_id',
        'description',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function studentPayment()
    {
        return $this->belongsTo(student_payment::class, 'student_payment_id', 'id');
    }

    public function typePayment()
    {
        return $this->belongsTo(type_payment::class, 'type_payment_id', 'id');
    }

    public function payment()
    {
        return $this->hasOneThrough(payment::class, student_payment::class, 'id', 'id', 'student_payment_id', 'payment_id');
    }


    public static function get(): mixed
    {
        return DB::table('payment_installment')
            ->join('student_payment', 'payment_installment.student_payment_id', '=', 'student_payment.id')
            ->join('student', 'student_payment.student_id', '=', 'student.id')
            ->join('payment', 'student_payment.payment_id', '=', 'payment.id')
            ->select(
                'payment_installment.id AS payment_installment_id',
                'payment_installment.amount',
                'payment_installment.payment_date',
                'payment_installment.description',
                'student_payment.id AS student_payment_id',
                'student.id AS student_id',
                'student.name AS student_name',
                'payment.id AS payment_id',
                'payment.name AS payment_name'
            )
            ->get();
    }
}
