<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class status extends Model
{
    use HasFactory;
    protected $table = 'status';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'name',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
}
