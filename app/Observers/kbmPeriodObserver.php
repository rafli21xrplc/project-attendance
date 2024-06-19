<?php

namespace App\Observers;

use App\Models\kbm_period;
use App\Traits\ObserverTrait;

class kbmPeriodObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(kbm_period $kbm_period): void
    {
        $kbm_period->id = $this->generateUuid();
    }
    /**
     * Handle the kbm_period "created" event.
     */
    public function created(kbm_period $kbm_period): void
    {
        //
    }

    /**
     * Handle the kbm_period "updated" event.
     */
    public function updated(kbm_period $kbm_period): void
    {
        //
    }

    /**
     * Handle the kbm_period "deleted" event.
     */
    public function deleted(kbm_period $kbm_period): void
    {
        //
    }

    /**
     * Handle the kbm_period "restored" event.
     */
    public function restored(kbm_period $kbm_period): void
    {
        //
    }

    /**
     * Handle the kbm_period "force deleted" event.
     */
    public function forceDeleted(kbm_period $kbm_period): void
    {
        //
    }
}
