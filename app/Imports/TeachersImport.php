<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;

class TeachersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Teacher([
            'id' => $row['id'],
            'nip' => $row['nip'],
            'nuptk' => $row['nuptk'],
            'name' => $row['name'],
            'gender' => $row['gender'],
            'telp' => $row['telp'],
            'user_id' => $row['user_id'],
        ]);
    }
}
