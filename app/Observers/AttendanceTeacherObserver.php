<?php

namespace App\Observers;

use App\Models\attendanceTeacher;
use App\Traits\ObserverTrait;

class AttendanceTeacherObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(attendanceTeacher $teacher): void
    {
        $teacher->id = $this->generateUuid();
    }
    /**
     * Handle the attendanceTeacher "created" event.
     */
    public function created(attendanceTeacher $attendanceTeacher): void
    {
        //
    }

    /**
     * Handle the attendanceTeacher "updated" event.
     */
    public function updated(attendanceTeacher $attendanceTeacher): void
    {
        //
    }

    /**
     * Handle the attendanceTeacher "deleted" event.
     */
    public function deleted(attendanceTeacher $attendanceTeacher): void
    {
        //
    }

    /**
     * Handle the attendanceTeacher "restored" event.
     */
    public function restored(attendanceTeacher $attendanceTeacher): void
    {
        //
    }

    /**
     * Handle the attendanceTeacher "force deleted" event.
     */
    public function forceDeleted(attendanceTeacher $attendanceTeacher): void
    {
        //
    }
}
