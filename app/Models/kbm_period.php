<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kbm_period extends Model
{
    use HasFactory;

    
    protected $table = 'kbm_period';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'name',
        'start_date',
        'end_date',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function getCurrentPeriod()
    {
        $today = now()->toDateString();
        return self::where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->first();
    }
}
