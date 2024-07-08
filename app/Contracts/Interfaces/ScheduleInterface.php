<?php

namespace App\Contracts\Interfaces;

interface ScheduleInterface extends GetInterface, StoreInterface, UpdateInterface, ShowInterface, DeleteInterface, GetCourseInterface, GetClassRoomInterface, GetTeacherInterface, GetTimeScheduleInterface, GetHoliday
{
}
