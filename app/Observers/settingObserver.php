<?php

namespace App\Observers;

use App\Models\setting;
use App\Traits\ObserverTrait;

class settingObserver
{
    use ObserverTrait;
    public function creating(setting $setting)
    {
        $setting->id = $this->generateUuid();
    }
    
    /**
     * Handle the setting "created" event.
     */
    public function created(setting $setting): void
    {
        //
    }

    /**
     * Handle the setting "updated" event.
     */
    public function updated(setting $setting): void
    {
        //
    }

    /**
     * Handle the setting "deleted" event.
     */
    public function deleted(setting $setting): void
    {
        //
    }

    /**
     * Handle the setting "restored" event.
     */
    public function restored(setting $setting): void
    {
        //
    }

    /**
     * Handle the setting "force deleted" event.
     */
    public function forceDeleted(setting $setting): void
    {
        //
    }
}
