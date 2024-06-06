<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class teaching_hour extends Model
{
    use HasFactory;
    protected $table = 'teaching_hour';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teaching_hours_id',
        'teacher_id',
        'course_id',
        'classroom_id',
        'hour',
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(teacher::class, 'teacher_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(course::class, 'course_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(classRoom::class, 'classroom_id');
    }
}
