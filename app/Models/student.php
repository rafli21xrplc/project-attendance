<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class student extends Model
{
    use HasFactory;
    protected $table = 'student';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'name',
        'gender',
        'classroom_id',
        'day_of_birth',
        'telp',
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

}
