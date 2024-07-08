<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\ClassInterface;
use App\Models\classRoom;
use App\Traits\ClassroomTrait;
use Illuminate\Database\QueryException;

class ClassRepository extends BaseRepository implements ClassInterface
{

    use ClassroomTrait;

    public function __construct(classRoom $classRoom)
    {
        $this->model = $classRoom;
    }

    public function getTeacher(): mixed
    {
        return $this->getTeachers();
    }

    public function getTypeClassroom(): mixed
    {
        return $this->TypeClassroom();
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return classRoom::get();
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
}
