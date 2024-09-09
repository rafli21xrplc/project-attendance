<?php

namespace App\Traits;

use App\Models\attendance;
use App\Models\attendanceLate;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\setting;
use App\Models\student;
use App\Models\teacher;
use App\Models\time_schedule;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait TeacherTrait
{
        public function getScheduleClassroom($id)
        {
                return schedule::findOrFail($id);
        }
        public function getStudents($id)
        {
                $classroom = classRoom::findOrFail($id);
                return $classroom->students;
        }

        public function getClassrooms()
        {
                return classRoom::all();
        }

        public function getClassroomStudent($id)
        {
                return classRoom::findOrFail($id);
        }

        public function importTeachers(array $data)
        {

                $data = Excel::toArray([], $data['file']);

                DB::transaction(function () use ($data) {
                        foreach ($data[0] as $row) {

                                $user = User::create([
                                        'uuid' => Str::uuid(),
                                        'username' => $row['1'],
                                        'password' => Hash::make('password')
                                ])->assignRole('teacher');

                                teacher::create([
                                        'id' => Str::uuid(),
                                        'nip' => $row['1'],
                                        'name' => $row['0'],
                                        'gender' => $row['2'],
                                        'user_id' => $user->id,
                                ]);
                        }
                });

                return back();
        }

        public function storeAttendanceStudent($students, $id)
        {
                try {
                        $currentPeriod = kbm_period::getCurrentPeriod();
                        $schedule = Schedule::with(['teacher', 'StartTimeSchedules', 'EndTimeSchedules'])->findOrFail($id);

                        $startSchedule = $schedule->StartTimeSchedules;
                        $endSchedule = $schedule->EndTimeSchedules;
                        if ($endSchedule->time_number == 10 && $startSchedule->time_number == 9) {
                                $hours = 1;
                        } else {
                                $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;
                        }

                        
                        $attendanceExists = $this->checkAttendance($schedule);
                        $data = [];
                        foreach ($students as $student) {
                                $data[] = [
                                        'id' => Str::uuid(),
                                        'student_id' => $student->id,
                                        'schedule_id' => $id,
                                        'kbm_period_id' => $currentPeriod->id,
                                        'time' => now()->format('Y-m-d H:i:s'),
                                        'hours' => $hours,
                                        'created_at' => now(),
                                ];
                        }


                        if ($attendanceExists) {
                                return $this->getAttendanceStudent($schedule);
                        } else {
                                attendance::insert($data);
                                return $this->getAttendanceStudent($schedule);
                        }
                } catch (\Throwable $th) {
                        // Log the error for debugging
                        \Log::error('Error storing attendance: ' . $th->getMessage());
                        return redirect(500)->back();
                }
        }

        public function checkAttendance($schedule)
        {
                $today = Carbon::now()->format('Y-m-d');

                // Use the existing relationship to avoid duplicate queries
                if ($schedule) {
                        return DB::table('attendance')
                                ->join('student', 'attendance.student_id', '=', 'student.id')
                                ->where('attendance.schedule_id', $schedule->id)
                                ->whereDate('attendance.created_at', $today)
                                ->exists();
                }

                return false;
        }

        public function getAttendanceStudent($schedule)
        {
                $today = Carbon::now()->format('Y-m-d');

                if ($schedule) {
                        return DB::table('attendance')
                                ->join('student', 'attendance.student_id', '=', 'student.id')
                                ->where('attendance.schedule_id', $schedule->id)
                                ->whereDate('attendance.created_at', $today)
                                ->select(
                                        'attendance.*',
                                        'student.name as student_name',
                                        'student.nisn as student_nisn',
                                        'student.classroom_id as student_classroom_id'
                                )
                                ->orderBy('student.name', 'asc')
                                ->get();
                }

                return $schedule;

                return null;
        }

        public function responseStore(User $user, array $data): mixed
        {
                $data = [
                        'nip' => $data['nip'],
                        'name' => $data['name'],
                        'gender' => $data['gender'],
                        'telp' => $data['telp'],
                        'user_id' => $user->id
                ];
                return $data;
        }

        public function responseUpdate(User $user, array $data): array
        {
                return [
                        'nip' => $data['nip'] ?? $user->nip,
                        'name' => $data['name'] ?? $user->name,
                        'gender' => $data['gender'] ?? $user->gender,
                        'telp' => $data['telp'] ?? $user->telp,
                        'user_id' => $user->id,
                ];
        }

        public function attendanceLate()
        {
                return  attendanceLate::attendanceLate();
        }

        public function getClassroomTeacher()
        {
                $teacherId = Auth::user()->teacher->id;
                $classroom = classRoom::with(['students', 'schedules.course', 'schedules.teacher'])
                        ->where('teacher_id', $teacherId)
                        ->first();

                if (!$classroom) {
                        return response()->json(['error' => 'Classroom not found for the given teacher'], 404);
                }

                return $classroom;
        }

        public function storeAttendanceHomeRoom(array $data, $classroomId)
        {
                $currentPeriod = kbm_period::getCurrentPeriod();

                foreach ($data as $studentId => $status) {
                        Attendance::insert([
                                'id' => Str::uuid(),
                                'kbm_period_id' => $currentPeriod->id,
                                'time' => now()->format('Y-m-d H:i:s'),
                                'status' => $status,
                                'student_id' => $studentId,
                                'schedule_id' => null,
                                'hours' => 10,
                                'created_at' => now(),
                        ]);
                }

                attendance::where('time', 'like', now()->format('Y-m-d H:i:s') . '%')->where('schedule_id', '!=', null)->delete();

                return back();
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
}
