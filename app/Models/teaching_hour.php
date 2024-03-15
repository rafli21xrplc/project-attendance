<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teaching_hour extends Model
{
    use HasFactory;
    protected $table = 'teaching_hour';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teaching_hours_id',
        'teacher_id',
        'student_id',
        'course_id',
        'hour',
    ];
    protected $primaryKey = 'id';
    protected $incrementing = false;
}
