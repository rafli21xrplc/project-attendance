<?php

namespace App\Imports;

use App\Models\student;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new student([
            'id' => $row['id'],
            'name' => $row['name'],
            'gender' => $row['gender'],
            'classroom_id' => $row['classroom_id'],
            'day_of_birth' => $row['day_of_birth'],
            'telp'  => $row['telp'],
            'user_id'  => $row['user_id'],
        ]);
    }
}
