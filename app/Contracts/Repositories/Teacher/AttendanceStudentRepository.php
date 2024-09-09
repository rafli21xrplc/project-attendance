<?php

namespace App\Contracts\Repositories\Teacher;

use App\Contracts\Interfaces\Teacher\StudentAttendanceInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\student;
use App\Traits\TeacherTrait;
use App\Models\attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceStudentRepository extends BaseRepository implements StudentAttendanceInterface
{
    use TeacherTrait;
    private Model $student;

    public function __construct(student $student)
    {
        $this->model = $student;
    }

    public function getAttendance($id): mixed
    {
        return $this->getStudents($id);
    }

    public function storeAttendance($attendances, $id): mixed
    {
        return $this->storeAttendanceStudent($attendances, $id);
    }

    public function getClassroom():mixed
    {
        return $this->getClassrooms();
    }

    public function getSchedule($id): mixed
    {
        return $this->getScheduleClassroom($id);
    }

    public function getClassroomById($id): mixed
    {
        return $this->getClassroomStudent($id);
    }
}
