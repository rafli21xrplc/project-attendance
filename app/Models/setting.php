<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class setting extends Model
{
    use HasFactory;
    protected $table = 'setting';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'key',
        'value',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
