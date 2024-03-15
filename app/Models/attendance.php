<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'course_id',
        'classroom_id',
        'time',
        'status',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
