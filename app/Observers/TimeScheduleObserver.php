<?php

namespace App\Observers;

use App\Models\time_schedule;
use App\Traits\ObserverTrait;

class TimeScheduleObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(time_schedule $time_schedule): void
    {
        $time_schedule->id = $this->generateUuid();
    }
    /**
     * Handle the time_schedule "created" event.
     */
    public function created(time_schedule $time_schedule): void
    {
        //
    }

    /**
     * Handle the time_schedule "updated" event.
     */
    public function updated(time_schedule $time_schedule): void
    {
        //
    }

    /**
     * Handle the time_schedule "deleted" event.
     */
    public function deleted(time_schedule $time_schedule): void
    {
        //
    }

    /**
     * Handle the time_schedule "restored" event.
     */
    public function restored(time_schedule $time_schedule): void
    {
        //
    }

    /**
     * Handle the time_schedule "force deleted" event.
     */
    public function forceDeleted(time_schedule $time_schedule): void
    {
        //
    }
}
