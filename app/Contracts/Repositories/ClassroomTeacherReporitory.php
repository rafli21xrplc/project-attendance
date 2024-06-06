<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\ClassroomTeacherInterface;
use App\Models\classRoom;
use App\Models\classroom_teacher;
use App\Models\teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class ClassroomTeacherReporitory extends BaseRepository implements ClassroomTeacherInterface
{
    private Model $teacher;
    private Model $classroom;
    public function __construct(classroom_teacher $homeroom_teacher, classRoom $classroom, teacher $teacher)
    {
        $this->model = $homeroom_teacher;
        $this->teacher = $teacher;
        $this->classroom = $classroom;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()->with(['teacher', 'classroom'])
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

    public function getTeacher(): mixed
    {
        return $this->teacher->query()->get();
    }
}
