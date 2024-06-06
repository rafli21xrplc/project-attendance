<?php

namespace App\Observers;

use App\Models\classroom_teacher;
use App\Traits\ObserverTrait;

class ClassroomTeacherObserver
{
    use ObserverTrait;

    public function creating(classroom_teacher $classroom_teacher)
    {
        $classroom_teacher->id = $this->generateId();
    }

    /**
     * Handle the classroom_teacher "created" event.
     */
    public function created(classroom_teacher $classroom_teacher): void
    {
    }

    /**
     * Handle the classroom_teacher "updated" event.
     */
    public function updated(classroom_teacher $classroom_teacher): void
    {
        //
    }

    /**
     * Handle the classroom_teacher "deleted" event.
     */
    public function deleted(classroom_teacher $classroom_teacher): void
    {
        //
    }

    /**
     * Handle the classroom_teacher "restored" event.
     */
    public function restored(classroom_teacher $classroom_teacher): void
    {
        //
    }

    /**
     * Handle the classroom_teacher "force deleted" event.
     */
    public function forceDeleted(classroom_teacher $classroom_teacher): void
    {
        //
    }
}
