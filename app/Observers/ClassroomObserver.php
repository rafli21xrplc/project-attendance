<?php

namespace App\Observers;

use Faker\Provider\Uuid;
use App\Models\classRoom;
use App\Traits\ObserverTrait;

class ClassroomObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(classRoom $classRoom): void
    {
        $classRoom->id = $this->generateUuid();
        $classRoom->class_id = $this->generateId();
    }

    public function created(classRoom $classRoom): void
    {
        //
    }

    /**
     * Handle the class_room "updated" event.
     */
    public function updated(classRoom $classRoom): void
    {
        //
    }

    /**
     * Handle the class_room "deleted" event.
     */
    public function deleted(classRoom $classRoom): void
    {
        //
    }

    /**
     * Handle the class_room "restored" event.
     */
    public function restored(classRoom $classRoom): void
    {
        //
    }

    /**
     * Handle the class_room "force deleted" event.
     */
    public function forceDeleted(classRoom $classRoom): void
    {
        //
    }
}
