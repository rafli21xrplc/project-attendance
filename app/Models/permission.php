<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permission extends Model
{
    use HasFactory;

    protected $table = 'permission';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'file',
        'description',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public function student()
    {
        return $this->belongsTo(student::class, 'student_id');
    }
}
