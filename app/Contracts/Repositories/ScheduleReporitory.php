<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\ScheduleInterface;
use App\Models\classRoom;
use App\Models\course;
use App\Models\schedule;
use App\Models\teacher;
use App\Models\time_schedule;
use App\Traits\ScheduleTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ScheduleReporitory extends BaseRepository implements ScheduleInterface
{
    use ScheduleTrait;
    protected teacher $teacher;
    protected course $course;
    protected classRoom $classroom;
    protected time_schedule $time_schedule;

    public function __construct(schedule $schedule, teacher $teacher, course $course, classRoom $classroom, time_schedule $time_schedule)
    {
        $this->model = $schedule;
        $this->teacher = $teacher;
        $this->course = $course;
        $this->classroom = $classroom;
        $this->time_schedule = $time_schedule;
    }

    public function getHoliday(): mixed
    {
        return $this->getHolidayDay();
    }

    public function getTimeSchedule(): mixed
    {
        return $this->time_schedule->query()->get();
    }

    public function getClassroom(): mixed
    {
        return classRoom::get();
    }

    public function getTeacher(): mixed
    {
        return $this->teacher->query()->get();
    }

    public function getCourse(): mixed
    {
        return $this->course->query()->get();
    }


    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return schedule::get();
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
