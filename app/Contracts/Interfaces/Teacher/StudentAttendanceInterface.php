<?php

namespace App\Contracts\Interfaces\Teacher;

use App\Contracts\Interfaces\GetClassRoomInterface;
use App\Contracts\Interfaces\GetClassroomStudentInterface;
use App\Contracts\Interfaces\GetScheduleClassroomInterface;
use App\Contracts\Interfaces\GetScheduleInterface;
use App\Contracts\Interfaces\GetTimeScheduleInterface;
use App\Contracts\Interfaces\ShowStudentAttendanceInterface;
use App\Contracts\Interfaces\StoreStudentAttendanceInterface;

interface StudentAttendanceInterface extends ShowStudentAttendanceInterface, StoreStudentAttendanceInterface, GetClassroomStudentInterface, GetScheduleClassroomInterface, GetClassRoomInterface
{
}
