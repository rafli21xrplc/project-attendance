<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\schedule;
use App\Models\teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait HistoryAttendanceTrait
{

        public function getClassroom($classroom_id)
        {
                return classRoom::findOrFail($classroom_id);
        }

        public function getSchedule($schedule_id)
        {
                return schedule::findOrFail($schedule_id);
        }

        public function getAttendaence($classroom_id, $schedule_id)
        {
                $classroom = $this->getClassroom($classroom_id);
                $schedule = $this->getSchedule($schedule_id);
                $student = $classroom->students;
                $attendanceData = attendance::where('schedule_id', $schedule_id)
                ->with(['student', 'schedule', 'permission']) 
                        ->whereIn('student_id', $student->pluck('id'))
                        ->get()
                        ->keyBy('student_id');

                return [
                        'classroom' => $classroom,
                        'schedule' => $schedule,
                        'student' => $student,
                        'attendanceData' => $attendanceData,
                ];
        }

        public function getScheduleClassroomHistory()
        {

                $user = Auth::user();

                if ($user->hasRole('teacher')) {
                        $teacher = teacher::where('user_id', Auth::user()->id)->first();
                        $today = Carbon::now()->format('l');

                        $schedules = schedule::where('teacher_id', $teacher->id)->with(['classroom', 'course', 'StartTimeSchedules', 'EndTimeSchedules'])
                                ->where('day_of_week', $today)
                                ->get();

                        $schedules = $schedules->filter(function ($schedule) {
                                return attendance::where('schedule_id', $schedule->id)
                                        ->whereDate('time', Carbon::today())
                                        ->exists();
                        });

                        return $schedules;
                }

                return collect();
        }
}
