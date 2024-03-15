<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teacher extends Model
{
    use HasFactory;

    protected $table = 'teacher';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'nip',
        'nuptk',
        'name',
        'gender',
        'born_at',
        'day_of_birth',
        'position',
        'status',
        'address',
        'telp',
    ];
    protected $primaryKey = 'id';
    protected $incrementing = false;
}
