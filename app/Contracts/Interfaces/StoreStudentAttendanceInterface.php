<?php

namespace App\Contracts\Interfaces;

interface StoreStudentAttendanceInterface
{
    public function storeAttendance(array $data, $id):mixed;
}
