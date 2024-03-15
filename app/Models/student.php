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
        'student_id',
        'name',
        'gender',
        'address',
        'classroom_id',
        'born_at',
        'day_of_birth',
        'telp',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
