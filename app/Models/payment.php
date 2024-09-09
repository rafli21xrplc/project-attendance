<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    use HasFactory;

    protected $table = 'payment';
    protected $fillable = ['id', 'name', 'category','amount', 'start_date','end_date'];
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function studentPayments()
    {
        return $this->hasMany(student_payment::class, 'payment_id', 'id');
    }
}
