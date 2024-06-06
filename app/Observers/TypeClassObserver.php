<?php

namespace App\Observers;

use App\Models\type_class;
use App\Traits\ObserverTrait;

class TypeClassObserver
{
    use ObserverTrait;
    /**
     * Handle the class_room "created" event.
     */

    public function creating(type_class $type_class): void
    {
        $type_class->id = $this->generateUuid();
    }
    /**
     * Handle the type_class "created" event.
     */
    public function created(type_class $type_class): void
    {
        //
    }

    /**
     * Handle the type_class "updated" event.
     */
    public function updated(type_class $type_class): void
    {
        //
    }

    /**
     * Handle the type_class "deleted" event.
     */
    public function deleted(type_class $type_class): void
    {
        //
    }

    /**
     * Handle the type_class "restored" event.
     */
    public function restored(type_class $type_class): void
    {
        //
    }

    /**
     * Handle the type_class "force deleted" event.
     */
    public function forceDeleted(type_class $type_class): void
    {
        //
    }
}
