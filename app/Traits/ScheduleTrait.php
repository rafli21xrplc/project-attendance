<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\attendanceLate;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\permission;
use App\Models\schedule;
use App\Models\setting;
use App\Models\student;
use App\Models\subtractionTime;
use App\Models\teacher;
use App\Models\type_class;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ScheduleTrait
{
        public function markAttendance()
        {
                try {
                        $date = Carbon::now()->format('Y-m-d');
                        $classrooms = classRoom::pluck('id');

                        foreach ($classrooms as $classroomId) {
                                $this->ensureSingleAttendancePerSchedule($classroomId, $date);
                                $this->adjustAttendanceHoursForSubtraction($classroomId);
                                $this->deleteInvalidAttendanceByDay($classroomId);
                        }

                        $this->cleanUpAttendanceLate();
                        $this->deleteAttendanceDuringSpecialDays();
                        $result = $this->checkAndMarkAutomaticAttendance();
                        $this->deletePermissionSchedule();

                        Log::info('SUCCESS ATTENDANCE RECORDS.');
                        return response()->json(['result' => $result]);
                } catch (\Throwable $th) {
                        Log::error('Error in markAttendance: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }

        public function checkAndMarkAutomaticAttendance()
        {
                Log::info('CHECK AND MARK AUTOMATIC ATTENDANCE.');

                try {
                        $currentPeriod = kbm_period::getCurrentPeriod();
                        $today = Carbon::now()->format('l');
                        $todayFormat = Carbon::now()->format('Y-m-d');
                        $currentTime = Carbon::now();

                        $setting = Setting::where('key', 'spesial_day')->value('value');
                        $specialDays = $setting ? json_decode($setting, true) : [];

                        foreach ($specialDays as $specialDay) {
                                if (Carbon::parse($todayFormat)->between(Carbon::parse($specialDay['start_date']), Carbon::parse($specialDay['end_date']))) {
                                        Log::info('Today is a special day. Skipping automatic attendance.');
                                        return;
                                }
                        }

                        $schedules = Schedule::where('day_of_week', $today)
                                ->with(['classroom.students:id', 'StartTimeSchedules', 'EndTimeSchedules'])
                                ->get();

                        $uncreatedAttendanceCount = 0;

                        foreach ($schedules as $schedule) {
                                $startSchedule = $schedule->StartTimeSchedules;
                                $endSchedule = $schedule->EndTimeSchedules;

                                if (!$startSchedule || !$endSchedule || is_null($startSchedule->time_number) || is_null($endSchedule->time_number)) {
                                        Log::warning('Invalid Start or End Time Schedule for schedule ID: ' . $schedule->id);
                                        continue;
                                }

                                $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;

                                if ($currentTime->greaterThanOrEqualTo(Carbon::parse($endSchedule->end_time_schedule))) {
                                        if (!$schedule->attendances()->whereDate('created_at', Carbon::today())->exists()) {
                                                $uncreatedAttendanceCount++;
                                                $students = $schedule->classroom->students->pluck('id');
                                                $data = [];

                                                foreach ($students as $studentId) {
                                                        $data[] = [
                                                                'id' => Str::uuid(),
                                                                'kbm_period_id' => $currentPeriod->id,
                                                                'time' => now(),
                                                                'status' => 'present',
                                                                'student_id' => $studentId,
                                                                'schedule_id' => $schedule->id,
                                                                'hours' => $hours,
                                                                'created_at' => now(),
                                                        ];
                                                }

                                                Attendance::insert($data);
                                                attendanceLate::create(['id' => Str::uuid(), 'schedule_id' => $schedule->id, 'created_at' => now()]);
                                        }
                                }
                        }

                        Log::info('Total schedules without attendance created today: ' . $uncreatedAttendanceCount);
                        return $uncreatedAttendanceCount;
                } catch (\Throwable $th) {
                        Log::error('Error in checkAndMarkAutomaticAttendance: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }

        public function deleteAttendanceDuringSpecialDays()
        {
                Log::info('DELETE ATTENDANCE DURING SPECIAL DAY.');

                try {
                        // Fetch the special day settings
                        $setting = Setting::where('key', 'spesial_day')->value('value');
                        $specialDays = $setting ? json_decode($setting, true) : [];

                        if (is_array($specialDays)) {
                                foreach ($specialDays as $specialDay) {
                                        $startDate = Carbon::parse($specialDay['start_date']);
                                        $endDate = Carbon::parse($specialDay['end_date']);

                                        // Delete attendance records where is_spesialDay is false and time is between the start and end date
                                        Attendance::whereBetween('time', [$startDate, $endDate])
                                                ->where('is_spesialDay', false)
                                                ->delete();

                                        Log::info('Deleted attendance records with is_spesialDay false between ' . $startDate->format('Y-m-d') . ' and ' . $endDate->format('Y-m-d'));
                                }
                        } else {
                                Log::info('No special days defined.');
                        }
                } catch (\Throwable $th) {
                        Log::error('Error in deleteAttendanceDuringSpecialDays: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }


        public function ensureSingleAttendancePerSchedule($classroomId, $date)
        {
                Log::info('ENSURE SINGLE ATTENDANCE PER SCHEDULE.');

                try {
                        $dayName = Carbon::parse($date)->format('l');

                        $schedules = Schedule::where('classroom_id', $classroomId)
                                ->where('day_of_week', $dayName)
                                ->get();

                        foreach ($schedules as $schedule) {
                                $attendances = Attendance::whereDate('time', $date)
                                        ->where('schedule_id', $schedule->id)
                                        ->get()
                                        ->groupBy('student_id');

                                foreach ($attendances as $studentId => $studentAttendances) {
                                        $this->cleanUpAttendances($studentAttendances);
                                }
                        }

                        Log::info('Success ensureSingleAttendancePerSchedule');
                } catch (\Throwable $th) {
                        Log::error('Error in ensureSingleAttendancePerSchedule: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }

        private function cleanUpAttendances($studentAttendances)
        {
                Log::info('CLEAN UP ATTENDANCE.');

                if ($studentAttendances->count() > 1) {
                        $withUpdatedAt = $studentAttendances->filter(fn($attendance) => !is_null($attendance->updated_at));
                        $withoutUpdatedAt = $studentAttendances->filter(fn($attendance) => is_null($attendance->updated_at));

                        $withoutUpdatedAt->each->delete();

                        if ($withUpdatedAt->count() > 1) {
                                $mostRecentAttendance = $withUpdatedAt->sortByDesc('updated_at')->first();
                                $withUpdatedAt->except($mostRecentAttendance->id)->each->delete();
                        }
                }
        }

        public function cleanUpAttendanceLate()
        {
                Log::info('CLEAN UP ATTENDANCE LATE.');

                try {
                        // Step 1: Find the IDs of the rows to keep (i.e., the minimum ID for each schedule_id and day)
                        $idsToKeep = DB::table('attendance_lates as al1')
                                ->select(DB::raw('MIN(al1.id) as id'))
                                ->groupBy('al1.schedule_id', DB::raw('DATE(al1.created_at)'))
                                ->pluck('id')
                                ->toArray(); // Convert the result to an array of IDs

                        // Step 2: Delete records that do not have the `MIN(id)` (i.e., duplicate records)
                        DB::table('attendance_lates')
                                ->whereNotIn('id', $idsToKeep) // Only delete records whose ID is not in the array of IDs to keep
                                ->delete();

                        Log::info('CLEAN UP ATTENDANCE LATE COMPLETED.');
                } catch (\Throwable $th) {
                        Log::error('CLEAN UP ATTENDANCE ERROR: ' . $th->getMessage());
                }
        }

        public function adjustAttendanceHoursForSubtraction($classroomId)
        {
                Log::info('ADJUST ATTENDANCE HOURS FOR SUBTRACTION.');

                $classroom = ClassRoom::with('students:id')->findOrFail($classroomId);
                $studentIds = $classroom->students->pluck('id');

                $subtractionTime = subtractionTime::getSubtractionTimeForToday();

                if ($subtractionTime) {
                        $allowedHours = $subtractionTime->end_time - $subtractionTime->start_time + 1;

                        foreach ($studentIds as $studentId) {
                                $attendances = Attendance::where('student_id', $studentId)
                                        ->whereDate('time', Carbon::today())
                                        ->orderBy('hours', 'desc')
                                        ->get();

                                $totalHours = $attendances->sum('hours');

                                if ($totalHours > $allowedHours) {
                                        $excessHours = $totalHours - $allowedHours;

                                        foreach ($attendances as $attendance) {
                                                if ($attendance->hours >= $excessHours) {
                                                        $attendance->hours -= $excessHours;
                                                        $attendance->save();
                                                        break;
                                                }
                                        }
                                }
                        }
                }
        }

        public function deletePermissionSchedule()
        {
                Log::info('DELETE PERMISSION SCHEDULE.');

                $twoWeeksAgo = Carbon::now()->subWeeks(2);

                $permissionsToDelete = permission::where('created_at', '<', $twoWeeksAgo)->get();

                foreach ($permissionsToDelete as $permission) {
                        if (Storage::exists($permission->file)) {
                                Storage::delete($permission->file);
                        }

                        $permission->delete();
                }

                return $permissionsToDelete;
        }

        public function deleteInvalidAttendanceByDay($classroomId)
        {
                Log::info('DELETE INVALID ATTENDANCE BY DAY.');

                try {
                        $students = student::where('classroom_id', $classroomId)->get();

                        foreach ($students as $value) {
                                $attendances = Attendance::getAttendanceStudent($value->id);

                                $deletedCount = 0;

                                foreach ($attendances as $attendance) {
                                        $attendanceDay = Carbon::parse($attendance->time)->format('l');

                                        $scheduleDay = $attendance->schedule_day_of_week;

                                        if ($attendanceDay !== $scheduleDay) {
                                                Attendance::where('id', $attendance->attendance_id)->delete();
                                                $deletedCount++;
                                                Log::info("Deleted attendance ID: {$attendance->id} for student ID: {$attendance->student_id} due to schedule day mismatch.");
                                        }
                                }

                                Log::info("Total invalid attendance records deleted: {$deletedCount}");
                                return $deletedCount;
                        }
                } catch (\Throwable $th) {
                        Log::error('Error in deleteInvalidAttendanceByDay: ' . $th->getMessage());
                        return $th->getMessage();
                }
        }


        public function getScheduleClassroom()
        {
                $user = Auth::user();

                if ($user->hasRole('teacher')) {
                        $teacher = Teacher::where('user_id', $user->id)->first();
                        $today = Carbon::now()->format('l');
                        $currentHour = Carbon::now()->format('H:i');

                        $schedule = Schedule::where('teacher_id', $teacher->id)
                                ->where('day_of_week', $today)
                                ->whereHas('StartTimeSchedules', function ($query) use ($currentHour) {
                                        $query->where('start_time_schedule', '<=', $currentHour);
                                })
                                ->whereHas('EndTimeSchedules', function ($query) use ($currentHour) {
                                        $query->where('end_time_schedule', '>=', $currentHour);
                                })
                                ->with(['StartTimeSchedules', 'EndTimeSchedules'])
                                ->first();

                        if ($schedule && !Attendance::where('schedule_id', $schedule->id)
                                ->whereDate('time', Carbon::today())
                                ->exists()) {
                                $classroom = Classroom::findOrFail($schedule->classroom_id);


                                return [
                                        'classroom' => $schedule->classroom,
                                        'student' => $classroom->students,
                                        'schedule' => $schedule
                                ];
                        }
                }

                return null;
        }

        public function getScheduleTeacher()
        {
                $user = Auth::user();
                $today = Carbon::now()->format('l');

                if ($user->hasRole('teacher')) {

                        $teacherId = $user->teacher->id;
                        $currentHour = Carbon::now()->format('H:i');

                        $schedules = schedule::where('teacher_id', $teacherId)
                                ->where('day_of_week', $today)
                                ->with(['StartTimeSchedules', 'EndTimeSchedules', 'classroom.typeClass', 'classroom.students', 'course', 'classroom'])
                                ->get();

                        return $schedules;
                }

                return null;
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
