<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class attendance extends Model
{
    use HasFactory;
    protected $table = 'attendance';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'schedule_id`',
        'kbm_period_id',
        'hours',
        'is_spesialDay',
        'time', // timestamps
        'status',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function permission()
    {
        return $this->hasOne(permission::class, 'student_id', 'student_id');
    }

    public function student()
    {
        return $this->belongsTo(student::class, 'student_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo(schedule::class, 'schedule_id', 'id');
    }

    public static function getAttendanceStudent($studentId)
    {
        return DB::table('attendances')
            ->join('schedules', 'attendances.schedule_id', '=', 'schedules.id')
            ->select(
                'attendances.id AS attendance_id',
                'attendances.time AS attendance_time',
                'schedules.day_of_week AS schedule_day_of_week'
            )
            ->where('attendances.student_id', $studentId)
            ->get();
    }
}
