<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\TeachingHourInterface;
use App\Models\classRoom;
use App\Models\course;
use App\Models\student;
use App\Models\teacher;
use App\Models\teaching_hour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class TeachingHourRepository extends BaseRepository implements TeachingHourInterface
{

    private Model $course;
    private Model $teacher;
    private Model $classroom;

    public function __construct(teaching_hour $teaching_hour, course $course, teacher $teacher, classRoom $classroom)
    {
        $this->model = $teaching_hour;
        $this->course = $course;
        $this->teacher = $teacher;
        $this->classroom = $classroom;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()->with(['classroom', 'teacher', 'classroom'])
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

    public function getClassroom(): mixed
    {
        return $this->classroom->query()->get();
    }

    public function getCourse(): mixed
    {
        return $this->course->query()->get();
    }

    public function getTeacher(): mixed
    {
        return $this->teacher->query()->get();
    }
}
