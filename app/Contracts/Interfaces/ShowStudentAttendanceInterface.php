<?php

namespace App\Contracts\Interfaces;

interface ShowStudentAttendanceInterface
{
    public function getAttendance(mixed $id):mixed;
}
