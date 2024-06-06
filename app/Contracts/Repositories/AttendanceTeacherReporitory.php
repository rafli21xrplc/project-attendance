<?php

namespace App\Contracts\Repositories;
use App\Contracts\Interfaces\AttendanceTeacherInterface;
use App\Models\attendanceTeacher;
use App\Models\teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class AttendanceTeacherReporitory extends BaseRepository implements AttendanceTeacherInterface
{
    private Model $teacher;
    public function __construct(attendanceTeacher $attendanceTeacher, teacher $teacher)
    {
        $this->model = $attendanceTeacher;
        $this->teacher = $teacher;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()
            ->get();
    }

    public function store(array $data): mixed
    {
        return $this->model->query()->create($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id): mixed
    {
        try {
            $this->show($id)->delete($id);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) return false;
        }

        return true;
    }

    public function getTeacher(): mixed
    {
        return $this->teacher->query()
            ->get();
    }
}
