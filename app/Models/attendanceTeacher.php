<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendanceTeacher extends Model
{
    use HasFactory;
    protected $table = 'attendance_teacher';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teacher_id',
        'status',
        'date',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
