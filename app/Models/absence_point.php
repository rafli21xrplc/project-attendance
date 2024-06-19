<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absence_point extends Model
{
    use HasFactory;
    protected $table = 'absence_point';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'hours_absent',
        'points',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
