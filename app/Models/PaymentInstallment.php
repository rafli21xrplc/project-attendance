<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function studentPayment()
    {
        return $this->belongsTo(student_payment::class, 'student_payment_id', 'id');
    }
}
