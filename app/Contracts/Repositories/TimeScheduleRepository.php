<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\TimeScheduleInterface;
use App\Models\time_schedule;
use App\Services\TimeScheduleDayService;
use App\Traits\TimeScheduleTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class TimeScheduleRepository extends BaseRepository implements TimeScheduleInterface
{
    use TimeScheduleTrait;
    private Model $time_schedule;
    public function __construct(time_schedule $time_schedule)
    {
        $this->time_schedule = $time_schedule;
    }

    public function show(mixed $id): mixed
    {
        return $this->time_schedule->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->time_schedule->query()
            ->get();
    }

    public function store(array $data): mixed
    {
        return $this->time_schedule->query()->create($data);
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
