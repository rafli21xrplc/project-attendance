<?php

namespace App\Observers;

use App\Models\teacher;
use App\Traits\ObserverTrait;
use Faker\Provider\Uuid;

class TeacherObserver
{
    use ObserverTrait;

    public function creating(teacher $teacher): void
    {
        $teacher->id = $this->generateUuid();
    }
    /**
     * Handle the teacher "created" event.
     */
    public function created(teacher $teacher): void
    {
    }

    /**
     * Handle the teacher "updated" event.
     */
    public function updated(teacher $teacher): void
    {
        //
    }

    /**
     * Handle the teacher "deleted" event.
     */
    public function deleted(teacher $teacher): void
    {
        //
    }

    /**
     * Handle the teacher "restored" event.
     */
    public function restored(teacher $teacher): void
    {
        //
    }

    /**
     * Handle the teacher "force deleted" event.
     */
    public function forceDeleted(teacher $teacher): void
    {
        //
    }
}
