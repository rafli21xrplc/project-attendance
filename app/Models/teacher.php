<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teacher extends Model
{
    use HasFactory;
    public $keyType = 'char';
    protected $table = 'teacher';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'nip',
        'name',
        'gender',
        'telp',
        'user_id',
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function religion()
    {
        return $this->belongsTo(religion::class, 'religion_id');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'user_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(schedule::class);
    }
    public function classroom()
    {
        return $this->hasOne(classRoom::class, 'teacher_id');
    }

    public function classrooms()
    {
        return $this->hasManyThrough(Classroom::class, Schedule::class, 'teacher_id', 'id', 'id', 'classroom_id');
    }
}
