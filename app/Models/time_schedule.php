<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class time_schedule extends Model
{
    use HasFactory;
    protected $table = 'time_schedules';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'time_number',
        'start_time_schedule',
        'end_time_schedule',
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;
}
