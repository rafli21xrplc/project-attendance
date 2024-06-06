<?php

namespace App\Contracts\Repositories\Teacher;

use App\Contracts\Interfaces\Teacher\AttendanceInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\schedule;
use App\Traits\ScheduleTrait;
use Illuminate\Database\Eloquent\Model;

class AttendanceReporitory extends BaseRepository implements AttendanceInterface
{
    use ScheduleTrait;
    private Model $schedule;
    public function __construct(schedule $schedule)
    {
        $this->schedule = $schedule;
    }

    public function getClassroom(): mixed
    {
        return $this->getScheduleClassroom();
    }
}
