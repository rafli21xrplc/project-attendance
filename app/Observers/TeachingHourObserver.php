<?php

namespace App\Observers;

use App\Models\teaching_hour;
use App\Traits\ObserverTrait;

class TeachingHourObserver
{
    use ObserverTrait;

    public function creating(teaching_hour $teaching_hour): void
    {
        $teaching_hour->id = $this->generateUuid();
        $teaching_hour->teaching_hours_id = $this->generateId();
    }
    /**
     * Handle the teaching_hour "created" event.
     */
    public function created(teaching_hour $teaching_hour): void
    {
        //
    }

    /**
     * Handle the teaching_hour "updated" event.
     */
    public function updated(teaching_hour $teaching_hour): void
    {
        //
    }

    /**
     * Handle the teaching_hour "deleted" event.
     */
    public function deleted(teaching_hour $teaching_hour): void
    {
        //
    }

    /**
     * Handle the teaching_hour "restored" event.
     */
    public function restored(teaching_hour $teaching_hour): void
    {
        //
    }

    /**
     * Handle the teaching_hour "force deleted" event.
     */
    public function forceDeleted(teaching_hour $teaching_hour): void
    {
        //
    }
}
