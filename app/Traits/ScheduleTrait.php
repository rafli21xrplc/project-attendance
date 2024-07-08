<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\permission;
use App\Models\schedule;
use App\Models\setting;
use App\Models\teacher;
use App\Models\time_schedule;
use App\Models\type_class;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait ScheduleTrait
{

        public function checkAndMarkAutomaticAttendance()
        {
                try {
                        $currentPeriod = kbm_period::getCurrentPeriod();
                        $today = Carbon::now()->format('l');
                        $currentTime = Carbon::now();

                        $schedules = Schedule::where('day_of_week', $today)
                                ->with(['classroom.students', 'attendances', 'StartTimeSchedules', 'EndTimeSchedules'])
                                ->get();

                        $uncreatedAttendanceCount = 0; // Counter untuk jadwal tanpa kehadiran

                        foreach ($schedules as $schedule) {
                                $startSchedule = time_schedule::find($schedule->start_time_schedule_id);
                                $endSchedule = time_schedule::find($schedule->end_time_schedule_id);

                                // Logging untuk waktu mulai dan akhir
                                Log::info('WAKTU MULAI: ', ['start_time_schedule' => $startSchedule]);
                                Log::info('WAKTU AKHIR: ', ['end_time_schedule' => $endSchedule]);

                                // Cek apakah time_number tidak null
                                if ($startSchedule && $endSchedule && $startSchedule->time_number !== null && $endSchedule->time_number !== null) {
                                        $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;

                                        if ($currentTime->greaterThanOrEqualTo(Carbon::parse($endSchedule->end_time_schedule))) {
                                                if (!$schedule->attendances()->whereDate('created_at', Carbon::today())->exists()) {
                                                        $uncreatedAttendanceCount++; // Increment the counter
                                                        foreach ($schedule->classroom->students as $student) {
                                                                Attendance::insert([
                                                                        'id' => Str::uuid(),
                                                                        'kbm_period_id' => $currentPeriod->id,
                                                                        'time' => now()->format('Y-m-d H:i:s'),
                                                                        'status' => 'present',
                                                                        'student_id' => $student->id,
                                                                        'schedule_id' => $schedule->id,
                                                                        'hours' => $hours,
                                                                        'created_at' => now(),
                                                                ]);
                                                        }
                                                }
                                        }
                                } else {
                                        // Tangani kasus di mana StartTimeSchedules atau EndTimeSchedules tidak ada atau time_number null
                                        Log::warning('Missing or invalid StartTimeSchedules or EndTimeSchedules for schedule ID: ' . $schedule->id);
                                        Log::warning('start_time_schedule_id: ' . $schedule->start_time_schedule_id);
                                        Log::warning('end_time_schedule_id: ' . $schedule->end_time_schedule_id);
                                }
                        }

                        // Catat atau kembalikan total jumlah jadwal tanpa kehadiran yang tercipta
                        Log::info('Total schedules without attendance created today: ' . $uncreatedAttendanceCount);
                        return $uncreatedAttendanceCount;
                } catch (\Throwable $th) {
                        Log::error('Error in checkAndMarkAutomaticAttendance: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }



        public function deletePermissionSchedule()
        {
                $twoWeeksAgo = Carbon::now()->subWeeks(2);
                $deleted = permission::where('created_at', '<', $twoWeeksAgo)->get();
                return $deleted;
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

        public function showClassrooms()
        {
                $teacherId = Auth::user()->teacher->id;

                $typeClasses = type_class::with(['classrooms' => function ($query) use ($teacherId) {
                        $query->whereHas('schedules', function ($query) use ($teacherId) {
                                $query->where('teacher_id', $teacherId);
                        })->with(['schedules' => function ($query) use ($teacherId) {
                                $query->where('teacher_id', $teacherId)->with('course');
                        }, 'students']);
                }])->get();

                return $typeClasses;
        }

        public function showTeacherSchedule()
        {
                $teacher = Auth::user()->teacher;

                $currentDay = now()->format('l');

                $schedules = Schedule::getTeacherSchedule($teacher->id, $currentDay);

                return $schedules;
        }

        public function getTop10Violations()
        {
                $classroomIds = classRoom::pluck('id')->toArray();

                if (empty($classroomIds)) {
                        $classroomIds = [ClassRoom::first()->id];
                }

                $currentPeriod = kbm_period::getCurrentPeriod();
                if (!$currentPeriod) {
                        return;
                }

                $attendances = Attendance::where('kbm_period_id', $currentPeriod->id)
                        ->whereHas('student.classroom', function ($query) use ($classroomIds) {
                                $query->whereIn('id', $classroomIds);
                        })
                        ->orderBy('student_id')
                        ->orderBy('time')
                        ->get();

                $students = [];

                foreach ($attendances as $attendance) {
                        $student = $attendance->student;
                        $studentId = $student->id;
                        $className = $student->classroom->typeClass->category . ' ' . $student->classroom->name;

                        if (!isset($students[$studentId])) {
                                $students[$studentId] = [
                                        'id' => $studentId,
                                        'name' => $student->name,
                                        'gender' => $student->gender,
                                        'class_name' => $className,
                                        'total_tatib_points' => 0,
                                ];
                        }

                        if (in_array($attendance->status, ['sick', 'permission', 'alpha'])) {
                                $hours = $attendance->hours;
                                $tatibPoints = $this->calculateTatibPoints($attendance->status, $hours);
                                $students[$studentId]['total_tatib_points'] += $tatibPoints;
                        }
                }

                $studentsArray = array_values($students);
                usort($studentsArray, function ($a, $b) {
                        return $b['total_tatib_points'] <=> $a['total_tatib_points'];
                });

                $top10Students = array_slice($studentsArray, 0, 10);

                return $top10Students;
        }

        private function calculateTatibPoints($status, $hours)
        {
                $pointsPerHour = [
                        'sick' => 0,
                        'permission' => 0,
                        'alpha' => 0.1,
                ];

                return $pointsPerHour[$status] * $hours;
        }

        private function getHolidayDay()
        {
                $holidays = setting::whereIn('key', ['first-holiday', 'second-holiday'])->pluck('value')->toArray();

                // Define all days of the week
                $daysOfWeek = [
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                        'Sunday' => 'Minggu'
                ];

                // Filter days of the week excluding holidays
                $availableDays = array_diff_key($daysOfWeek, array_flip($holidays));

                return $availableDays;
        }
}
