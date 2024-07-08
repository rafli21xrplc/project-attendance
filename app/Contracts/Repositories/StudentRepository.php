<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\StudentInterface;
use App\Models\classRoom;
use App\Models\religion;
use App\Models\student;
use App\Models\type_class;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class StudentRepository extends BaseRepository implements StudentInterface
{
    private Model $religi;
    private Model $type_class;
    private Model $class_room;
    private Model $user;
    private StudentService $studentService;

    public function __construct(student $student, religion $religion, classRoom $class_room, User $user, StudentService $studentService, type_class $type_class)
    {
        $this->model = $student;
        $this->type_class = $type_class;
        $this->religi = $religion;
        $this->user = $user;
        $this->class_room = $class_room;
        $this->studentService = $studentService;
    }
    public function getTypeClassroom(): mixed
    {
        return $this->type_class->query()->get();
    }
    public function getClassroom(): mixed
    {
        return $this->class_room->query()->get();
    }
    public function getReligi(): mixed
    {
        return $this->religi->query()->get();
    }
    public function getUser(): mixed
    {
        return $this->user->query()->get();
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return student::get();
    }

    public function store(array $data): mixed
    {
        $data = $this->studentService->store($data);
        return $this->model->query()->create($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        $data = $this->studentService->update($id, $data);
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id): mixed
    {
        try {
            $data = $this->show($id);
            $data->delete($id);
            $this->studentService->delete($data->user_id);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) return false;
        }

        return true;
    }
}
