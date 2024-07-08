<?php

namespace App\Observers;

use App\Models\PaymentInstallment;
use App\Traits\ObserverTrait;

class paymentInstallmentsObserver
{
    use ObserverTrait;

    public function creating(PaymentInstallment $PaymentInstallment): void
    {
        $PaymentInstallment->id = $this->generateUuid();
        $PaymentInstallment->payment_date = now();
    }
    /**
     * Handle the PaymentInstallment "created" event.
     */
    public function created(PaymentInstallment $paymentInstallment): void
    {
        //
    }

    /**
     * Handle the PaymentInstallment "updated" event.
     */
    public function updated(PaymentInstallment $paymentInstallment): void
    {
        //
    }

    /**
     * Handle the PaymentInstallment "deleted" event.
     */
    public function deleted(PaymentInstallment $paymentInstallment): void
    {
        //
    }

    /**
     * Handle the PaymentInstallment "restored" event.
     */
    public function restored(PaymentInstallment $paymentInstallment): void
    {
        //
    }

    /**
     * Handle the PaymentInstallment "force deleted" event.
     */
    public function forceDeleted(PaymentInstallment $paymentInstallment): void
    {
        //
    }
}
