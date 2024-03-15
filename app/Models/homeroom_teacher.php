<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class homeroom_teacher extends Model
{
    use HasFactory;
    protected $table = 'homeroom_teacher';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'teacher_id',
        'class_id',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
