<?php

namespace App\Contracts\Repositories;
use App\Contracts\Interfaces\ExamLoginInterface;
use App\Models\ExamLogin;
use App\Traits\ExamLoginTrait;
use Illuminate\Database\QueryException;

class ExamLoginRepository extends BaseRepository implements ExamLoginInterface
{
    use ExamLoginTrait;
    public function __construct(ExamLogin $ExamLogin)
    {
        $this->model = $ExamLogin;
    }

    public function getStudent(): mixed
    {
        return $this->getStudents();
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return ExamLogin::get();
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
