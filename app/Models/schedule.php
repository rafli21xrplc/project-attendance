<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsTo(course::class, 'course_id');
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
        return $this->belongsTo(time_schedule::class, 'start_time_schedule_id');
    }

    public function EndTimeSchedules()
    {
        return $this->belongsTo(time_schedule::class, 'end_time_schedule_id');
    }

    public static function getTeacherSchedule($teacher_id, $currentDay)
    {
        return DB::select('
            SELECT 
                s.id AS schedule_id,
                s.day_of_week,
                c.name AS course_name,
                ts_start.time_number AS start_time_number,
                ts_start.start_time_schedule AS start_time,
                ts_end.time_number AS end_time_number,
                ts_end.end_time_schedule AS end_time,
                t.name AS teacher_name,
                cr.name AS classroom_name,
                tc.category AS type_class_category
            FROM 
                schedule s
            JOIN 
                course c ON s.course_id = c.id
            JOIN 
                time_schedules ts_start ON s.start_time_schedule_id = ts_start.id
            JOIN 
                time_schedules ts_end ON s.end_time_schedule_id = ts_end.id
            JOIN 
                teacher t ON s.teacher_id = t.id
            JOIN 
                class_room cr ON s.classroom_id = cr.id
            JOIN 
                type_class tc ON cr.type_class_id = tc.id
            WHERE 
                s.teacher_id = ? AND 
                s.day_of_week = ?', [$teacher_id, $currentDay]);
    }

    public static function get(): mixed
    {
        return DB::table('schedule')
            ->select(
                'schedule.*',
                'teacher.name as teacher_name',
                'class_room.name as classroom_name',
                'course.name as course_name',
                'start_time_schedules.time_number as start_time_number',
                'end_time_schedules.time_number as end_time_number',
                'start_time_schedules.start_time_schedule as start_time',
                'end_time_schedules.end_time_schedule as end_time',
                'type_class.category as type_class_category'
            )
            ->join('teacher', 'schedule.teacher_id', '=', 'teacher.id')
            ->join('class_room', 'schedule.classroom_id', '=', 'class_room.id')
            ->join('course', 'schedule.course_id', '=', 'course.id')
            ->join('time_schedules as start_time_schedules', 'schedule.start_time_schedule_id', '=', 'start_time_schedules.id')
            ->join('time_schedules as end_time_schedules', 'schedule.end_time_schedule_id', '=', 'end_time_schedules.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->get();
    }

    public static function getSchedulesLate($dayOfWeek)
    {
        return DB::table('schedule')
            ->select(
                'schedule.id as schedule_id',
                'schedule.day_of_week',
                'teacher.name as teacher_name',
                'class_room.name as classroom_name',
                'course.name as course_name',
                'start_time_schedules.start_time_schedule as start_time',
                'end_time_schedules.end_time_schedule as end_time',
                'attendance.created_at as attendance_created_at'
            )
            ->join('teacher', 'schedule.teacher_id', '=', 'teacher.id')
            ->join('class_room', 'schedule.classroom_id', '=', 'class_room.id')
            ->join('course', 'schedule.course_id', '=', 'course.id')
            ->join('time_schedules as start_time_schedules', 'schedule.start_time_schedule_id', '=', 'start_time_schedules.id')
            ->join('time_schedules as end_time_schedules', 'schedule.end_time_schedule_id', '=', 'end_time_schedules.id')
            ->leftJoin('attendance', function($join) {
                $join->on('schedule.id', '=', 'attendance.schedule_id')
                     ->whereDate('attendance.created_at', Carbon::today());
            })
            ->where('schedule.day_of_week', $dayOfWeek)
            ->get();
    }

    public static function getSchedules(array $data)
    {
        $dayOfWeek = Carbon::parse($data['date'])->format('l');

        $schedules = DB::select('
            SELECT 
                s.id AS schedule_id,
                s.day_of_week,
                s.classroom_id,
                s.course_id,
                s.teacher_id,
                s.start_time_schedule_id,
                s.end_time_schedule_id,
                cr.name AS classroom_name,
                tc.category AS type_class_category,
                c.name AS course_name,
                t.name AS teacher_name,
                sts.time_number AS start_time_number,
                sts.start_time_schedule AS start_time,
                ets.time_number AS end_time_number,
                ets.end_time_schedule AS end_time,
                a.student_id,
                a.status AS attendance_status
            FROM 
                schedule s
            JOIN 
                class_room cr ON s.classroom_id = cr.id
            JOIN 
                type_class tc ON cr.type_class_id = tc.id
            JOIN 
                course c ON s.course_id = c.id
            JOIN 
                teacher t ON s.teacher_id = t.id
            JOIN 
                time_schedules sts ON s.start_time_schedule_id = sts.id
            JOIN 
                time_schedules ets ON s.end_time_schedule_id = ets.id
            LEFT JOIN 
                attendance a ON s.id = a.schedule_id
            WHERE 
                s.classroom_id = :classroom_id AND s.day_of_week = :day_of_week
        ', ['classroom_id' => $data['classroom_id'], 'day_of_week' => $dayOfWeek]);

        return collect($schedules);
    }
}
