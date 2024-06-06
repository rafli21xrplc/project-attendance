<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\ObserverTrait;

class UserObserver
{
    use ObserverTrait;

    public function creating(User $user): void
    {
        $user->uuid = $this->generateUuid();
    }
    /**
     * Handle the users "created" event.
     */
    public function created(User $User): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $User): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $User): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $User): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $User): void
    {
        //
    }
}
