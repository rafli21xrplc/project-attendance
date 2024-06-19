<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\teacher;
use App\Models\time_schedule;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait ScheduleTrait
{
        public function checkAndMarkAutomaticAttendance()
        {
                $currentPeriod = kbm_period::getCurrentPeriod();
                $today = Carbon::now()->format('l');
                $currentTime = Carbon::now();

                $schedules = Schedule::where('day_of_week', $today)->with(['classroom.students', 'attendances', 'StartTimeSchedules', 'EndTimeSchedules'])->get();

                foreach ($schedules as $schedule) {
                        $startSchedule = $schedule->StartTimeSchedules;
                        $endSchedule = $schedule->EndTimeSchedules;

                        $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;


                        if ($currentTime->greaterThanOrEqualTo(Carbon::parse($endSchedule->end_time_schedule))) {

                                dd($currentTime->greaterThanOrEqualTo(Carbon::parse($endSchedule->end_time_schedule)));


                                if (!$schedule->attendances()->whereDate('created_at', Carbon::today())->exists()) {
                                        foreach ($schedule->classroom->students as $student) {
                                                try {
                                                        attendance::insert([
                                                                'id' => Str::uuid(),
                                                                'kbm_period_id' => $currentPeriod->id,
                                                                'time' => now()->format('Y-m-d H:i:s'),
                                                                'status' => 'present',
                                                                'student_id' => $student->id,
                                                                'schedule_id' => $schedule->id,
                                                                'hours' => $hours,
                                                                'created_at' => now(),
                                                        ]);
                                                } catch (\Throwable $th) {
                                                        dd($th->getMessage());
                                                }
                                        }
                                }
                        }
                }
        }

        public function getScheduleClassroom()
        {

                $user = Auth::user();

                if ($user->hasRole('teacher')) {
                        $teacher = teacher::where('user_id', $user->id)->first();
                        $today = Carbon::now()->format('l');
                        $currentHour = Carbon::now()->format('H:i');

                        $schedule = schedule::where('teacher_id', $teacher->id)
                                ->where('day_of_week', $today)
                                ->whereHas('StartTimeSchedules', function ($query) use ($currentHour) {
                                        $query->where('start_time_schedule', '<=', $currentHour);
                                })
                                ->whereHas('EndTimeSchedules', function ($query) use ($currentHour) {
                                        $query->where('end_time_schedule', '>=', $currentHour);
                                })
                                ->with(['StartTimeSchedules', 'EndTimeSchedules'])
                                ->first();

                        if ($schedule && !attendance::where('schedule_id', $schedule->id)
                                ->whereDate('time', Carbon::today())
                                ->exists()) {

                                $classroom = classRoom::findOrFail($schedule->classroom_id);

                                return [
                                        'classroom' => $schedule->classroom,
                                        'student' => $classroom->students,
                                        'schedule' => $schedule
                                ];
                        }
                }
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
