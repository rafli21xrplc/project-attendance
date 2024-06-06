<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class classroom_teacher extends Model
{
    use HasFactory;
    protected $table = 'classroom_teacher';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teacher_id',
        'class_id',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function teacher():BelongsTo
    {
        return $this->belongsTo(teacher::class, 'teacher_id');
    }

    public function classroom():BelongsTo
    {
        return $this->belongsTo(classRoom::class, 'class_id');
    }
}
