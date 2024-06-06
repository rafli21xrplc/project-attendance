<?php

namespace App\Observers;

use App\Models\payment;
use App\Traits\ObserverTrait;

class PaymentObserver
{

    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(payment $payment): void
    {
        $payment->id = $this->generateUuid();
    }

    /**
     * Handle the payment "created" event.
     */
    public function created(payment $payment): void
    {
        //
    }

    /**
     * Handle the payment "updated" event.
     */
    public function updated(payment $payment): void
    {
        //
    }

    /**
     * Handle the payment "deleted" event.
     */
    public function deleted(payment $payment): void
    {
        //
    }

    /**
     * Handle the payment "restored" event.
     */
    public function restored(payment $payment): void
    {
        //
    }

    /**
     * Handle the payment "force deleted" event.
     */
    public function forceDeleted(payment $payment): void
    {
        //
    }
}
