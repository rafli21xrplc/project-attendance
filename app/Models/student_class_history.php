<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student_class_history extends Model
{
    use HasFactory;
    protected $table = 'student_class_histories';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'classroom_id',
        'start_date',
        'end_date',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
