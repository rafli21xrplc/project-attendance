<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Student::class, 'classroom_id', 'id');
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
        return $this->hasMany(Schedule::class);
    }
}
