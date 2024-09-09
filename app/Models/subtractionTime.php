<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subtractionTime extends Model
{
    use HasFactory;
    protected $table = 'subtraction_time';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'tanggal',
        'start_time',
        'end_time',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public static function getSubtractionTimeForToday()
    {
        return SubtractionTime::whereDate('tanggal', Carbon::today())->first();
    }
}
