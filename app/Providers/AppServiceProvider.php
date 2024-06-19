<?php

namespace App\Providers;

use App\Contracts\Interfaces\AbsencePointInterface;
use App\Contracts\Interfaces\AdminInterface;
use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Contracts\Interfaces\AttendanceTeacherInterface;
use App\Contracts\Interfaces\ClassInterface;
use App\Contracts\Interfaces\ClassroomTeacherInterface;
use App\Contracts\Interfaces\CourseInterface;
use App\Contracts\Interfaces\KbmPeriodInterface;
use App\Contracts\Interfaces\PaymentInterface;
use App\Contracts\Interfaces\ScheduleInterface;
use App\Contracts\Interfaces\Teacher\StudentAttendanceInterface;
use App\Contracts\Interfaces\StudentInterface;
use App\Contracts\Interfaces\StudentPaymentInterface;
use App\Contracts\Interfaces\Teacher\AttendanceInterface;
use App\Contracts\Interfaces\TeacherInterface;
use App\Contracts\Interfaces\TeachingHourInterface;
use App\Contracts\Interfaces\TimeScheduleInterface;
use App\Contracts\Interfaces\TypeClassInterface;
use App\Contracts\Repositories\AbsencePointRepository;
use App\Contracts\Repositories\AdminReporitory;
use App\Contracts\Repositories\AttendanceRekapRepository;
use App\Contracts\Repositories\StudentPaymentRepository;
use App\Contracts\Repositories\Teacher\AttendanceStudentRepository;
use App\Contracts\Repositories\AttendanceTeacherReporitory;
use App\Contracts\Repositories\ClassRepository;
use App\Contracts\Repositories\ClassroomTeacherReporitory;
use App\Contracts\Repositories\CourseRepository;
use App\Contracts\Repositories\KbmPeriodRepository;
use App\Contracts\Repositories\PaymentRepository;
use App\Contracts\Repositories\ScheduleReporitory;
use App\Contracts\Repositories\StudentRepository;
use App\Contracts\Repositories\Teacher\AttendanceReporitory;
use App\Contracts\Repositories\TeacherRepository;
use App\Contracts\Repositories\TeachingHourRepository;
use App\Contracts\Repositories\TimeScheduleRepository;
use App\Contracts\Repositories\TypeClassRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    private array $register = [
        ScheduleInterface::class => ScheduleReporitory::class,
        AdminInterface::class => AdminReporitory::class,
        TeacherInterface::class => TeacherRepository::class,
        CourseInterface::class => CourseRepository::class,
        ClassInterface::class => ClassRepository::class,
        StudentInterface::class => StudentRepository::class,
        TeachingHourInterface::class => TeachingHourRepository::class,
        ClassroomTeacherInterface::class => ClassroomTeacherReporitory::class,
        AttendanceTeacherInterface::class => AttendanceTeacherReporitory::class,
        AttendanceInterface::class => AttendanceReporitory::class,
        StudentAttendanceInterface::class => AttendanceStudentRepository::class,
        TimeScheduleInterface::class => TimeScheduleRepository::class,
        TypeClassInterface::class => TypeClassRepository::class,
        PaymentInterface::class => PaymentRepository::class,
        StudentPaymentInterface::class => StudentPaymentRepository::class,
        AttendanceRekapInterface::class => AttendanceRekapRepository::class,
        KbmPeriodInterface::class => KbmPeriodRepository::class,
        AbsencePointInterface::class => AbsencePointRepository::class
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->register as $index => $value) $this->app->bind($index, $value);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
