<?php

namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\teacher;
use App\Models\time_schedule;
use App\Models\User;
use Carbon\Carbon;
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

        public function storeAttendanceStudent(array $attendances, $id)
        {
                try {
                        $currentPeriod = kbm_period::getCurrentPeriod();

                        $schedule = schedule::findOrFail($id);

                        $startSchedule = time_schedule::findOrFail($schedule->start_time_schedule_id);
                        $endSchedule = time_schedule::findOrFail($schedule->end_time_schedule_id);

                        $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;

                        $dataToInsert = [];
                        foreach ($attendances as $studentId => $status) {
                                $dataToInsert[] = [
                                        'id' => Str::uuid(),
                                        'student_id' => $studentId,
                                        'schedule_id' => $id,
                                        'kbm_period_id' => $currentPeriod->id,
                                        'time' => now()->format('Y-m-d H:i:s'),
                                        'status' => $status,
                                        'hours' => $hours,
                                        'created_at' => now(),
                                ];
                        }

                        attendance::insert($dataToInsert);

                        return true;
                } catch (\Throwable $th) {
                        return false;
                }
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
                $today = Carbon::now()->format('l');
                $schedules = schedule::getSchedulesLate($today);
                $missingAttendances = [];

                foreach ($schedules as $schedule) {
                        $endScheduleTime = Carbon::parse($schedule->end_time);

                        if (Carbon::now()->greaterThan($endScheduleTime)) {
                                $attendanceCreatedAt = $schedule->attendance_created_at ? Carbon::parse($schedule->attendance_created_at) : null;

                                if (!$attendanceCreatedAt || $attendanceCreatedAt->greaterThan($endScheduleTime)) {
                                        $missingAttendances[] = [
                                                'teacher_name' => $schedule->teacher_name,
                                                'classroom' => $schedule->classroom_name,
                                                'course' => $schedule->course_name,
                                                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                                                'end_time' => $endScheduleTime->format('H:i'),
                                        ];
                                }
                        }
                }

                return $missingAttendances;
        }
}
