<?php

namespace App\Observers;

use App\Models\student;
use App\Traits\ObserverTrait;

class StudentObserver
{
    use ObserverTrait;
    public function creating(student $student)
    {
        $student->id = $this->generateUuid();
        $student->student_id = $this->generateId();
    }
    /**
     * Handle the student "created" event.
     */
    public function created(student $student): void
    {
    }

    /**
     * Handle the student "updated" event.
     */
    public function updated(student $student): void
    {
        //
    }

    /**
     * Handle the student "deleted" event.
     */
    public function deleted(student $student): void
    {
        //
    }

    /**
     * Handle the student "restored" event.
     */
    public function restored(student $student): void
    {
        //
    }

    /**
     * Handle the student "force deleted" event.
     */
    public function forceDeleted(student $student): void
    {
        //
    }
}
