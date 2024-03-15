<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course extends Model
{
    use HasFactory;
    protected $table = 'course';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'course_id',
        'name',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
