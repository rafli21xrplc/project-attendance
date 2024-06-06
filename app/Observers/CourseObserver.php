<?php

namespace App\Observers;

use App\Models\course;
use App\Traits\ObserverTrait;
use Faker\Provider\Uuid;

class CourseObserver
{
    use ObserverTrait;
    public function creating(course $course): void
    {
        $course->id = $this->generateUuid();
        $course->course_id = $this->generateId();
    }
    /**
     * Handle the course "created" event.
     */
    public function created(course $course): void
    {
        //
    }

    /**
     * Handle the course "updated" event.
     */
    public function updated(course $course): void
    {
        //
    }

    /**
     * Handle the course "deleted" event.
     */
    public function deleted(course $course): void
    {
        //
    }

    /**
     * Handle the course "restored" event.
     */
    public function restored(course $course): void
    {
        //
    }

    /**
     * Handle the course "force deleted" event.
     */
    public function forceDeleted(course $course): void
    {
        //
    }
}
