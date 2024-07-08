<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExamLogin extends Model
{
    use HasFactory;
    protected $table = 'exam_login';
    protected $guarded = [];
    protected $fillable = [
        'id',
        'student_id',
        'username',
        'password',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;

    public static function get()
    {
        return DB::table('exam_login')
            ->select(
                'exam_login.id as exam_login_id',
                'exam_login.username',
                'exam_login.password',
                'student.id as student_id',
                'student.name as student_name',
            )
            ->join('student', 'exam_login.student_id', '=', 'student.id')
            ->get();
    }
}
