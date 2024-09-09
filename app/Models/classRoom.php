<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class classRoom extends Model
{
    use HasFactory;
    public $keyType = 'char';
    protected $table = 'class_room';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'type_class_id',
        'class_id',
        'name',
        'teacher_id', 
    ];

    protected $primaryKey = 'id';

    public $incrementing = false;

    public function students()
    {
        return $this->hasMany(Student::class, 'classroom_id', 'id')->orderBy('name', 'asc');
    }
    
    public function teacher()
    {
        return $this->belongsTo(teacher::class, 'teacher_id');
    }
    
    public function typeClass()
    {
        return $this->belongsTo(type_class::class, 'type_class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'classroom_id', 'id');
    }

    public static function get()
    {
        return DB::table('class_room')
            ->join('type_class', 'class_room.type_class_id', '=', 'type_class.id')
            ->join('teacher', 'class_room.teacher_id', '=', 'teacher.id')
            ->select(
                'class_room.id AS id',
                'class_room.name AS name',
                'type_class.id AS type_class_id',
                'type_class.category AS type_class_category',
                'teacher.id AS teacher_id',
                'teacher.name AS teacher_name'
            )
            ->get();
    }
}
