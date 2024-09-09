<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logStudent extends Model
{
    use HasFactory;
    protected $table = 'log_student';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'log',
        'time',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
