<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\schedule;
use App\Models\teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait ScheduleTrait
{

        public function getScheduleClassroom()
        {
                $user = Auth::user();

                // if ($user->hasRole('teacher')) {
                //         $teacher = $user->teacher;

                //         $today = Carbon::now()->englishDayOfWeek;

                //         $schedules = $teacher->schedules()
                //                 ->where('day_of_week', $today)
                //                 // ->where('completed', false)
                //                 ->with(['classroom.students', 'course'])
                //                 ->get();

                //         return $schedules;
                // }

                if ($user->hasRole('teacher')) {
                        $teacher = teacher::where('user_id', Auth::user()->id)->first();
                        $today = Carbon::now()->format('l');

                        $schedules = schedule::where('teacher_id', $teacher->id)
                                ->where('day_of_week', $today)
                                ->get();

                        $schedules = $schedules->filter(function ($schedule) {
                                return !attendance::where('schedule_id', $schedule->id)
                                        ->whereDate('time', Carbon::today())
                                        ->exists();
                        });

                        return $schedules;
                }

                return collect();
        }

        public function getClassroom()
        {
                $user = Auth::user();

                if ($user->hasRole('teacher')) {
                        $teacher = $user->teacher;

                        $schedules = $teacher->schedules()
                                ->with(['classroom.typeClass', 'classroom.students'])
                                ->get();

                        $classrooms = $schedules->pluck('classroom')->unique('id');
                }
                return collect();
        }
}
