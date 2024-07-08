<?php

namespace App\Observers;

use App\Models\ExamLogin;
use App\Traits\ExamLoginTrait;
use App\Traits\ObserverTrait;

class ExamLoginObserver
{
    use ObserverTrait;

    public function creating(ExamLogin $ExamLogin): void
    {
        $ExamLogin->id = $this->generateUuid();
    }
    /**
     * Handle the ExamLogin "created" event.
     */
    public function created(ExamLogin $examLogin): void
    {
        //
    }

    /**
     * Handle the ExamLogin "updated" event.
     */
    public function updated(ExamLogin $examLogin): void
    {
        //
    }

    /**
     * Handle the ExamLogin "deleted" event.
     */
    public function deleted(ExamLogin $examLogin): void
    {
        //
    }

    /**
     * Handle the ExamLogin "restored" event.
     */
    public function restored(ExamLogin $examLogin): void
    {
        //
    }

    /**
     * Handle the ExamLogin "force deleted" event.
     */
    public function forceDeleted(ExamLogin $examLogin): void
    {
        //
    }
}
