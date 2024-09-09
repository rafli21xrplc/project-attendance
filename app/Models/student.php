<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class student extends Model
{
    use HasFactory;
    protected $table = 'student';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'nisn',
        'name',
        'gender',
        'day_of_birth',
        'graduated',
        'telp',
        'classroom_id',
        'user_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function attendance()
    {
        return $this->hasMany(attendance::class, 'student_id');
    }

    public function religion()
    {
        return $this->belongsTo(religion::class, 'religion_id', 'id');
    }

    public function classroom()
    {
        return $this->belongsTo(classroom::class, 'classroom_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id', 'id');
    }

    public function schedules()
    {
        return $this->hasManyThrough(schedule::class, classRoom::class, 'id', 'classroom_id', 'classroom_id');
    }

    public function todaysPermissions()
    {
        return $this->hasMany(permission::class, 'student_id')
            ->whereDate('created_at', Carbon::today());
    }

    public function studentPayments()
    {
        return $this->hasMany(student_payment::class, 'student_id', 'id');
    }

    public static function get(): mixed
    {
        return DB::table('student')
            ->join('users', 'student.user_id', '=', 'users.id')
            ->join('class_room', 'student.classroom_id', '=', 'class_room.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->select(
                'student.id AS student_id',
                'student.name AS student_name',
                'student.gender',
                'student.graduated',
                'student.day_of_birth',
                'student.telp',
                'users.id AS user_id',
                'users.username',
                'class_room.id AS classroom_id',
                'class_room.name AS classroom_name',
                'type_class.id AS type_class_id',
                'type_class.category AS type_class_category'
            )
            ->get();
    }

    public static function search($id): mixed
    {
        return DB::table('student')
            ->join('users', 'student.user_id', '=', 'users.id')
            ->join('class_room', 'student.classroom_id', '=', 'class_room.id')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->select(
                'student.id AS student_id',
                'student.name AS student_name',
                'student.gender',
                'student.graduated',
                'student.day_of_birth',
                'student.telp',
                'users.id AS user_id',
                'users.username',
                'class_room.id AS classroom_id',
                'class_room.name AS classroom_name',
                'type_class.id AS type_class_id',
                'type_class.category AS type_class_category'
            )
            ->where('student.classroom_id', $id)
            ->get();
    }
}
