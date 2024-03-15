<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class classRoom extends Model
{
    use HasFactory;
    protected $table = 'class_room';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'class_id',
        'name',
    ];

    protected $primaryKey = 'id';
    protected $incrementing = false;
}
