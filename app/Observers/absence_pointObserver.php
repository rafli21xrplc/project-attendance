<?php

namespace App\Observers;

use App\Models\absence_point;
use App\Traits\ObserverTrait;

class absence_pointObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(absence_point $absence_point): void
    {
        $absence_point->id = $this->generateUuid();
    }
    /**
     * Handle the absence_point "created" event.
     */
    public function created(absence_point $absence_point): void
    {
        //
    }

    /**
     * Handle the absence_point "updated" event.
     */
    public function updated(absence_point $absence_point): void
    {
        //
    }

    /**
     * Handle the absence_point "deleted" event.
     */
    public function deleted(absence_point $absence_point): void
    {
        //
    }

    /**
     * Handle the absence_point "restored" event.
     */
    public function restored(absence_point $absence_point): void
    {
        //
    }

    /**
     * Handle the absence_point "force deleted" event.
     */
    public function forceDeleted(absence_point $absence_point): void
    {
        //
    }
}
