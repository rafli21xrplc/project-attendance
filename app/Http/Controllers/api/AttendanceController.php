<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\attendanceStoreRequest;
use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\setting;
use App\Models\teacher;
use App\Models\time_schedule;
use App\Traits\AttendanceTrait;
use App\Traits\TeacherTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{

//    use AttendanceTrait;

    public function getSchedule(Request $request)
    {
        $userId = $request->query('user_id');

        $teacher = Teacher::where('user_id', $userId)->first();
        if (!$teacher) {
            return response()->json([
                'classroom' => null,
                'student' => null,
                'schedule' => null,
                'message' => 'Teacher not found'
            ]);
        }

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
            ->with(['StartTimeSchedules', 'EndTimeSchedules', 'classroom.typeClass', 'classroom.students', 'course'])
            ->first();

        $attendance = null;

        if ($schedule) {

            $todayDate = Carbon::now()->format('Y-m-d');

            $attendance = DB::table('attendance')
                ->join('student', 'attendance.student_id', '=', 'student.id')
                ->leftJoin('log_student', function ($join) use ($todayDate) {
                    $join->on('log_student.student_id', '=', 'student.id')
                        ->whereDate('log_student.created_at', '=', $todayDate);
                })
                ->where('attendance.schedule_id', $schedule->id)
                ->whereDate('attendance.created_at', '=', $todayDate)
                ->select(
                    'attendance.*',
                    'student.name as student_name',
                    'student.nisn as student_nisn',
                    'student.classroom_id as student_classroom_id',
                    'log_student.log as tardy_status',
                    'log_student.time as tardy_time'
                )
                ->orderBy('student.name', 'asc')
                ->get();
        }

        if ($schedule && $attendance->isEmpty()) {
            $classroom = $schedule->classroom;
            $students = $classroom->students()->with('logStudents')->get();

            return response()->json([
                'classroom' => $classroom,
                'student' => $students,
                'schedule' => $schedule,
                'attendance' => [],
            ]);
        } else {
            $classroom = $schedule->classroom;
            $students = $classroom->students()->with('logStudents')->get();
            return response()->json([
                'classroom' => $schedule ? $schedule->classroom : null,
                'student' => $schedule ? $students : null,
                'schedule' => $schedule,
                'attendance' => $attendance,
            ]);
        }
    }

    public function getSpecialDay(Request $request)
    {
        $userId = $request->query('user_id');

        $teacher = Teacher::where('user_id', $userId)->first();

        $isDay = $this->checkIsSpesialDay();
        $isDayDone = $this->checkIsSpesialDayDone();
        $classroom = $this->getClassroomTeacher($teacher->id);

        if (!$teacher || !$isDay) {
            return response()->json([
                'classroom' => null,
                'student' => null,
                'schedule' => null,
                'message' => 'Teacher not found'
            ]);
        }

        $attendance = DB::table('attendance')
            ->join('student', 'attendance.student_id', '=', 'student.id')
            ->whereDate('attendance.time', Carbon::today())
            ->where('is_spesialDay', true)
            ->select(
                'attendance.*',
                'student.name as student_name',
                'student.nisn as student_nisn',
                'student.classroom_id as student_classroom_id'
            )
            ->get();

        if (!$isDay) {

            return response()->json([
                'isDay' => $isDay,
                'classroom' => $classroom,
                'student' => $classroom->students,
                'attendance' => [],
            ]);
        } else {
            return response()->json([
                'isDay' => $isDay,
                'classroom' => $classroom,
                'student' => $classroom->students,
                'attendance' => $attendance,
            ]);
        }
    }

    public function getTeacherData(Request $request)
    {
        $userId = $request->query('user_id');

        $teacher = Teacher::where('user_id', $userId)->first();

        $today = Carbon::now()->format('l');
        $currentHour = Carbon::now()->format('H:i');
        $currentDay = now()->format('l');

        $schedules = Schedule::getTeacherSchedule($teacher->id, $currentDay);

        $totalSchedulesToday = Schedule::where('teacher_id', $teacher->id)
            ->where('day_of_week', $today)
            ->count();

        $totalClasses = Schedule::where('teacher_id', $teacher->id)->count();

        return response()->json([
            'homeroom' => isset($teacher->classroom),
            'today' => $today,
            'total_classes' => $totalClasses,
            'total_schedules_today' => $totalSchedulesToday,
            'SchedulesToday' => $schedules
        ]);
    }

    public function store(Request $request, $id)
    {
        try {
            $attendances = $request->attendance;
            $currentPeriod = kbm_period::getCurrentPeriod();
            $schedule = Schedule::findOrFail($id);
            $startSchedule = time_schedule::findOrFail($schedule->start_time_schedule_id);
            $endSchedule = time_schedule::findOrFail($schedule->end_time_schedule_id);
            $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;

            $values = [];
            $now = now()->format('Y-m-d H:i:s');
            foreach ($attendances as $studentId => $status) {
                $uuid = Str::uuid();
                $values[] = "('$uuid', '$studentId', '$id', '{$currentPeriod->id}', '$now', '$status', $hours, '$now')";
            }

            $sql = "INSERT INTO attendance (id, student_id, schedule_id, kbm_period_id, time, status, hours, created_at) VALUES " . implode(',', $values);
            DB::insert($sql);

            // Verification Step
            $insertedData = DB::table('attendance')
                ->where('time', 'LIKE', now()->format('Y-m-d') . '%')
                ->where('schedule_id', $id)
                ->get();

            foreach ($attendances as $studentId => $status) {
                $expectedHours = $hours;
                $match = $insertedData->first(function ($record) use ($studentId, $status, $expectedHours) {
                    return $record->student_id == $studentId &&
                        $record->status == $status &&
                        $record->hours == $expectedHours;
                });

                if (!$match) {
                    return response()->json(['message' => 'Failed to verify recorded attendance'], 500);
                }
            }

            return response()->json(['message' => 'Attendance recorded and verified successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to record attendance', 'error' => $th->getMessage()], 500);
        }
    }

    public function storeSpecialDay(Request $request)
    {
        try {
            $attendances = $request->attendance;

            $currentPeriod = kbm_period::getCurrentPeriod();

            $values = [];
            $now = now()->format('Y-m-d H:i:s');
            foreach ($attendances as $studentId => $status) {
                $uuid = Str::uuid();
                $values[] = "('$uuid', '$studentId', NULL, '{$currentPeriod->id}', '$now', '$status', 10, '$now', true)";
            }

            $sql = "INSERT INTO attendance (id, student_id, schedule_id, kbm_period_id, time, status, hours, created_at, is_spesialDay) VALUES " . implode(',', $values);
            DB::insert($sql);

            $insertedData = DB::table('attendance')
                ->where('time', 'LIKE', now()->format('Y-m-d') . '%')
                ->where('is_spesialDay', true)
                ->get();

            foreach ($attendances as $studentId => $status) {
                $match = $insertedData->first(function ($record) use ($studentId, $status) {
                    return $record->student_id == $studentId &&
                        $record->status == $status;
                });

                if (!$match) {
                    return response()->json(['message' => 'Failed to verify recorded attendance'], 500);
                }
            }

            return response()->json(['message' => 'Attendance recorded and verified successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to record attendance', 'error' => $th->getMessage()], 500);
        }
    }



    public function update(Request $request)
    {
        try {

            $currentPeriod = kbm_period::getCurrentPeriod();
            $attendances = $request->attendance;


            foreach ($attendances as $studentId => $attendanceData) {

                $attendanceId = $attendanceData['id'] ?? null;
                $status = $attendanceData['status'] ?? null;

                $existingAttendance = attendance::findOrFail($attendanceId);
                $existingAttendance->update([

                    'status' => $status,
                    'kbm_period_id' => $currentPeriod->id,
                    'updated_at' => now(),
                ]);
            }


            return response()->json(['message' => 'Attendance recorded successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateSpecialDay(Request $request)
    {
        try {

            $currentPeriod = kbm_period::getCurrentPeriod();
            $attendances = $request->attendance;


            foreach ($attendances as $studentId => $attendanceData) {

                $attendanceId = $attendanceData['id'] ?? null;
                $status = $attendanceData['status'] ?? null;

                $existingAttendance = attendance::findOrFail($attendanceId);
                $existingAttendance->update([

                    'status' => $status,
                    'kbm_period_id' => $currentPeriod->id,
                    'updated_at' => now(),
                ]);
            }


            return response()->json(['message' => 'Attendance recorded successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function getAttendance($id, $month)
    {

        $teacher = teacher::where('user_id', $id)->firstOrFail();

        $reportData = $this->AttendanceHomeroomReport($teacher->classroom->id, $month);

        return response()->json([
            'report' => $reportData['report'],
            'classroom' => $reportData['classroom'],
            'startDate' => $reportData['startDate'],
            'endDate' => $reportData['endDate'],
        ]);
    }

    public function checkIsSpesialDay()
    {
        $todayFormat = Carbon::now()->format('Y-m-d');

        $setting = setting::where('key', 'spesial_day')->first();
        $specialDays = $setting ? json_decode($setting->value, true) : [];

        if (is_array($specialDays)) {
            foreach ($specialDays as $specialDay) {
                if (Carbon::parse($todayFormat)->between(Carbon::parse($specialDay['start_date']), Carbon::parse($specialDay['end_date']))) {
                    return true;
                }
            }
        }

        return false;
    }

    public function checkIsSpesialDayDone()
    {
        $classroom = Auth::user()->teacher->classroom ?? null;

        if ($classroom) {
            $todayFormat = Carbon::now()->format('Y-m-d');
            $attendanceExists = Attendance::whereIn('student_id', $classroom->students->pluck('id'))
                ->whereDate('time', $todayFormat)
                ->exists();
        } else {
            $attendanceExists = false;
        }

        return $attendanceExists;
    }

    public function getClassroomTeacher($id)
    {
        $classroom = classRoom::with(['students', 'typeClass'])
            ->where('teacher_id', $id)
            ->first();

        if (!$classroom) {
            return response()->json(['error' => 'Classroom not found for the given teacher'], 404);
        }

        return $classroom;
    }
}
