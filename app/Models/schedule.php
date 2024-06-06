<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    use HasFactory;
    protected $table = 'schedule';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teacher_id',
        'classroom_id',
        'course_id',
        'day_of_week',
        'start_time_schedule_id',
        'end_time_schedule_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function attendances()
    {
        return $this->hasMany(attendance::class, 'schedule_id');
    }

    public function course()
    {
        return $this->belongsTo(course::class, 'course_id', 'id');
    }
    public function classroom()
    {
        return $this->belongsTo(classroom::class, 'classroom_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(teacher::class, 'teacher_id', 'id');
    }

    public function StartTimeSchedules()
    {
        return $this->belongsTo(time_schedule::class, 'start_time_schedule_id', 'id');
    }

    public function EndTimeSchedules()
    {
        return $this->belongsTo(time_schedule::class, 'end_time_schedule_id', 'id');
    }
}
