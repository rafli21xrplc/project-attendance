<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class religion extends Model
{
    use HasFactory;
    protected $table = 'religion';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'name',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
