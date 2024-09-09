<?php

namespace App\Observers;

use App\Models\type_payment;
use App\Traits\ObserverTrait;

class typePaymentObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(type_payment $type_payment): void
    {
        $type_payment->id = $this->generateUuid();
    }
    /**
     * Handle the type_payment "created" event.
     */
    public function created(type_payment $type_payment): void
    {
        //
    }

    /**
     * Handle the type_payment "updated" event.
     */
    public function updated(type_payment $type_payment): void
    {
        //
    }

    /**
     * Handle the type_payment "deleted" event.
     */
    public function deleted(type_payment $type_payment): void
    {
        //
    }

    /**
     * Handle the type_payment "restored" event.
     */
    public function restored(type_payment $type_payment): void
    {
        //
    }

    /**
     * Handle the type_payment "force deleted" event.
     */
    public function forceDeleted(type_payment $type_payment): void
    {
        //
    }
}
