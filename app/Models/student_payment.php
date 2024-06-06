<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student_payment extends Model
{
    use HasFactory;
    
    protected $table = 'student_payment';
    protected $fillable = ['id', 'student_id', 'payment_id', 'mount', 'is_paid', 'payment_date'];
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $incrementing = false;
}
