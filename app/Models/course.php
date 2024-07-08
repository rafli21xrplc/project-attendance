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
    public $incrementing = false;

    public function schedules()
    {
        return $this->hasMany(schedule::class, 'course_id');
    }
}
