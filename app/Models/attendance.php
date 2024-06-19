<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'note',
        'time',
        'status',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function permission()
    {
        return $this->hasOne(permission::class, 'student_id', 'student_id')
            ->whereColumn('schedule_id', 'schedule_id');
    }

    public function student()
    {
        return $this->belongsTo(student::class);
    }

    public function schedule()
    {
        return $this->belongsTo(schedule::class);
    }
}
