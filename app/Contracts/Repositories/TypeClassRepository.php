<?php

namespace App\Contracts\Repositories;
use App\Contracts\Interfaces\TypeClassInterface;
use App\Models\type_class;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class TypeClassRepository extends BaseRepository implements TypeClassInterface
{
    public function __construct(type_class $type_class)
    {
        $this->model = $type_class;
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
