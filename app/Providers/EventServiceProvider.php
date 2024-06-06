<?php

namespace App\Providers;

use App\Models\attendanceTeacher;
use App\Models\classRoom;
use App\Models\classroom_teacher;
use App\Models\course;
use App\Models\payment;
use App\Models\schedule;
use App\Models\student;
use App\Models\student_payment;
use App\Models\teacher;
use App\Models\teaching_hour;
use App\Models\time_schedule;
use App\Models\type_class;
use App\Models\User;
use App\Observers\AttendanceTeacherObserver;
use App\Observers\ClassroomObserver;
use App\Observers\ClassroomTeacherObserver;
use App\Observers\CourseObserver;
use App\Observers\PaymentObserver;
use App\Observers\ScheduleObserver;
use App\Observers\StudentObserver;
use App\Observers\StudentPaymentObserver;
use App\Observers\TeacherObserver;
use App\Observers\TeachingHourObserver;
use App\Observers\TimeScheduleObserver;
use App\Observers\TypeClassObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        teacher::observe(TeacherObserver::class);
        course::observe(CourseObserver::class);
        classRoom::observe(ClassroomObserver::class);
        student::observe(StudentObserver::class);
        teaching_hour::observe(TeachingHourObserver::class);
        classroom_teacher::observe(ClassroomTeacherObserver::class);
        attendanceTeacher::observe(AttendanceTeacherObserver::class);
        schedule::observe(ScheduleObserver::class);
        time_schedule::observe(TimeScheduleObserver::class);
        type_class::observe(TypeClassObserver::class);
        payment::observe(PaymentObserver::class);
        student_payment::observe(StudentPaymentObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
