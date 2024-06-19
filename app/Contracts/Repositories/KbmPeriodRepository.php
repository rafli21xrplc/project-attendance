<?php

namespace App\Contracts\Repositories;
use App\Contracts\Interfaces\KbmPeriodInterface;
use App\Models\kbm_period;
use Illuminate\Database\QueryException;

class KbmPeriodRepository extends BaseRepository implements KbmPeriodInterface
{
    public function __construct(kbm_period $kbm_period)
    {
        $this->model = $kbm_period;
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
