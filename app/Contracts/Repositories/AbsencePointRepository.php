<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\AbsencePointInterface;
use App\Models\absence_point;
use App\Models\classRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class AbsencePointRepository extends BaseRepository implements AbsencePointInterface
{


    public function __construct(absence_point $absence_point)
    {
        $this->model = $absence_point;
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
}
