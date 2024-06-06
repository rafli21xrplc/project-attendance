<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\TeacherInterface;
use App\Models\religion;
use App\Models\teacher;
use App\Services\TeacherService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class TeacherRepository extends BaseRepository implements TeacherInterface
{
    private Model $religi;
    private TeacherService $teacherService;

    public function __construct(teacher $teacher, religion $religion, TeacherService $teacherService)
    {
        $this->model = $teacher;
        $this->religi = $religion;
        $this->teacherService = $teacherService;
    }

    public function getReligi(): mixed
    {
        return $this->religi->query()->get();
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()->with(['religion', 'user'])
            ->get();
    }

    public function store(array $data): mixed
    {
        $data = $this->teacherService->store($data);
        return $this->model->query()->create($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        $data = $this->teacherService->update($id, $data);
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id): mixed
    {
        try {
            $data = $this->show($id);
            $data->delete($id);
            $this->teacherService->delete($data->user_id);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) return false;
        }

        return true;
    }
}
