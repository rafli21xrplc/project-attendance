<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class attendanceLate extends Model
{

    use HasFactory;
    protected $table = 'attendance_lates';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'schedule_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function schedule()
    {
        return $this->belongsTo(schedule::class, 'schedule_id', 'id');
    }

    public static function attendanceLate()
    {
        return DB::table('attendance_lates')
            ->join('schedule', 'attendance_lates.schedule_id', '=', 'schedule.id')
            ->join('teacher', 'schedule.teacher_id', '=', 'teacher.id')
            ->join('class_room', 'schedule.classroom_id', '=', 'class_room.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->join('course', 'schedule.course_id', '=', 'course.id')
            ->join('time_schedules as start_time', 'schedule.start_time_schedule_id', '=', 'start_time.id')
            ->join('time_schedules as end_time', 'schedule.end_time_schedule_id', '=', 'end_time.id')
            ->select(
                'attendance_lates.id',
                'attendance_lates.schedule_id',
                'schedule.day_of_week as day',
                'teacher.name as teacher_name',
                'class_room.name as class_name',
                'type_class.category as type_class_category',
                'course.name as course_name',
                'start_time.start_time_schedule as start_time',
                'end_time.end_time_schedule as end_time',
                'attendance_lates.created_at'
            )
            ->get();
    }

    public static function attendanceLateByDate($start, $end)
    {
        return DB::table('attendance_lates')
            ->join('schedule', 'attendance_lates.schedule_id', '=', 'schedule.id')
            ->join('teacher', 'schedule.teacher_id', '=', 'teacher.id')
            ->join('class_room', 'schedule.classroom_id', '=', 'class_room.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->join('course', 'schedule.course_id', '=', 'course.id')
            ->join('time_schedules as start_time', 'schedule.start_time_schedule_id', '=', 'start_time.id')
            ->join('time_schedules as end_time', 'schedule.end_time_schedule_id', '=', 'end_time.id')
            ->whereBetween('attendance_lates.created_at', [$start, $end])
            ->select(
                DB::raw('MIN(attendance_lates.id) as id'), // Menggunakan fungsi agregasi MIN untuk kolom id
                'attendance_lates.schedule_id',
                'schedule.day_of_week as day',
                'teacher.name as teacher_name',
                'class_room.name as class_name',
                'type_class.category as type_class_category',
                'course.name as course_name',
                'start_time.start_time_schedule as start_time',
                'end_time.end_time_schedule as end_time',
                DB::raw('DATE(attendance_lates.created_at) as attendance_date'),
                DB::raw('MIN(attendance_lates.created_at) as created_at') // Fungsi agregasi untuk created_at
            )
            ->groupBy('schedule.id', DB::raw('DATE(attendance_lates.created_at)')) // Group by schedule_id and date
            ->get();
    }
    


    public static function attendanceLateByDateToday($date)
    {
        return DB::table('attendance_lates')
            ->join('schedule', 'attendance_lates.schedule_id', '=', 'schedule.id')
            ->join('teacher', 'schedule.teacher_id', '=', 'teacher.id')
            ->join('class_room', 'schedule.classroom_id', '=', 'class_room.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->join('course', 'schedule.course_id', '=', 'course.id')
            ->join('time_schedules as start_time', 'schedule.start_time_schedule_id', '=', 'start_time.id')
            ->join('time_schedules as end_time', 'schedule.end_time_schedule_id', '=', 'end_time.id')
            ->whereDate('attendance_lates.created_at', $date)
            ->select(
                'attendance_lates.id',
                'attendance_lates.schedule_id',
                'schedule.day_of_week as day',
                'teacher.name as teacher_name',
                'class_room.name as class_name',
                'type_class.category as type_class_category',
                'course.name as course_name',
                'start_time.start_time_schedule as start_time',
                'end_time.end_time_schedule as end_time',
                'attendance_lates.created_at'
            )
            ->get();
    }
}
