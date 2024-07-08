<?php


namespace App\Traits;

use App\Models\ExamLogin;
use App\Models\student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait ExamLoginTrait
{

        public function getStudents(): mixed
        {
                return student::get();
        }

        public function generateUsername(): mixed
        {
                return student::get();
        }

        public function generatePassword(): mixed
        {
                return student::get();
        }

        public function importTeachers(array $data): mixed
        {
                $data = Excel::toArray([], $data['file']);

                DB::transaction(function () use ($data) {
                        foreach ($data[0] as $row) {

                                ExamLogin::create([
                                        'id' => Str::uuid(),
                                        'student_id' => $row[0],
                                        'username' => $row[1],
                                        'password' => $row[2],
                                ]);
                        }
                });

                return back();
        }
}
