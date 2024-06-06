<?php

namespace App\Observers;

use App\Models\student_payment;
use App\Traits\ObserverTrait;

class StudentPaymentObserver
{

    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(student_payment $student_payment): void
    {
        $student_payment->id = $this->generateUuid();
    }

    /**
     * Handle the student_payment "created" event.
     */
    public function created(student_payment $student_payment): void
    {
        //
    }

    /**
     * Handle the student_payment "updated" event.
     */
    public function updated(student_payment $student_payment): void
    {
        //
    }

    /**
     * Handle the student_payment "deleted" event.
     */
    public function deleted(student_payment $student_payment): void
    {
        //
    }

    /**
     * Handle the student_payment "restored" event.
     */
    public function restored(student_payment $student_payment): void
    {
        //
    }

    /**
     * Handle the student_payment "force deleted" event.
     */
    public function forceDeleted(student_payment $student_payment): void
    {
        //
    }
}
