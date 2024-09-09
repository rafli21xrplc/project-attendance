<?php

namespace App\Contracts\Interfaces\Teacher;

use App\Contracts\Interfaces\GetScheduleInterface;
use App\Contracts\Interfaces\ShowClassroomInterface;

interface AttendanceInterface extends ShowClassroomInterface, GetScheduleInterface
{
}
