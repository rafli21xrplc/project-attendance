<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Traits\AttendanceTrait;
use App\Traits\ScheduleTrait;

class AttendanceRekapRepository extends BaseRepository implements AttendanceRekapInterface
{
    use AttendanceTrait;
    public function getAttendanceStudent(array $data): mixed
    {
        return $this->searchAttendance($data);
    }

    public function getClassroom(): mixed
    {
        return $this->getClassrooms();
    }

    public function getStudent(): mixed {
        return $this->getStudents();
    }
}
