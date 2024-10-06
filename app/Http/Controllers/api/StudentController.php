<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\logStudent;
use App\Models\permission;
use App\Models\schedule;
use App\Models\student;
use App\Traits\AttendanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{

    use AttendanceTrait;

    public function getAttendanceStudent($id)
    {
        $student = Student::where('user_id', $id)->firstOrFail();

        $attendance = $this->generateAttendanceStudent($student->id);

        return $attendance;
    }

    public function storePermission(Request $request, $id)
    {

        $validatedData = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:12048',
            'description' => 'required|string'
        ]);

        $file = $validatedData['file'];

        $today = Carbon::today();

        $student = Student::where('user_id', $id)->firstOrFail();

        try {
            $existingPermission = Permission::where('student_id', $student->id)
                ->whereDate('created_at', $today)
                ->first();

            if ($existingPermission) {
                throw new \Exception('Surat izin untuk hari ini sudah diajukan');
            }

            if (isset($file)) {
                $filePath = $file->store('images/permission', 'public');
            } else {
                throw new \Exception('File surat izin diperlukan.');
            }

            Permission::create([
                'id' => Str::uuid(),
                'student_id' => $student->id,
                'file' => $filePath,
                'description' => $validatedData['description']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Surat izin berhasil disimpan.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function schedule(Request $request)
    {

        try {

            $userId = $request->query('user_id');

            $student = student::where('user_id', $userId)->first();

            $today = Carbon::now()->format('l');
            $currentHour = Carbon::now()->format('H:i');
            $currentDay = now()->format('l');

            $schedules = schedule::getStudentSchedule($student->id, $currentDay);

            $totalSchedulesToday = Schedule::where('classroom_id', $student->classroom_id)
                ->where('day_of_week', $today)
                ->count();

            $totalClasses = Schedule::where('classroom_id', $student->classroom_id)->count();

            return response()->json([
                'total_classes' => $totalClasses,
                'total_schedules_today' => $totalSchedulesToday,
                'SchedulesToday' => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSchedule(Request $request)
    {

        try {
            $userId = $request->query('user_id');

            $student = student::where('user_id', $userId)->first();

            if ($student->hasRole('student')) {
                $permission = permission::where('student_id', $student->id)->get();
                return response()->json([
                    'permissions' => $permission,
                    'student' => $student
                ], 200);
            } else {
                return response()->json([
                    'messages' => 'failed load'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPermission($id)
    {

        try {
            $student = Student::where('user_id', $id)->firstOrFail();

            $permission = permission::where('student_id', $student->id)->get();

            return response()->json([
                'permission' => $permission,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function in($id)
    {
        try {
            $currentTime = Carbon::now();
            $today = $currentTime->toDateString();

            $student = Student::where('user_id', $id)->firstOrFail();

            $log = $currentTime->hour >= 7 ? 'telat' : 'tepat';

            $isset = logStudent::where('student_id', $student->id)
                ->whereDate('created_at', $today)
                ->whereIn('log', ['telat', 'tepat'])
                ->exists();

            if (!$isset) {
                logStudent::create([
                    'id' => Str::uuid(),
                    'student_id' => $student->id,
                    'log' => $log,
                    'time' => $currentTime->toTimeString(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => "Kedatangan siswa dicatat sebagai '$log'.",
                ], 200);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => "Kedatangan siswa sudah dicatat.",
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function out($id)
    {

        try {
            $currentTime = Carbon::now();
            $today = $currentTime->toDateString();

            $student = Student::where('user_id', $id)->firstOrFail();

            if ($currentTime->hour < 15) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Belum waktu pulang siswa.",
                ], 200);
            } else {
                $log = 'pulang';

                $isset = logStudent::where('student_id', $student->id)
                    ->whereDate('created_at', $today)
                    ->where('log', 'pulang')
                    ->exists();

                if (!$isset) {
                    logStudent::create([
                        'id' => Str::uuid(),
                        'student_id' => $student->id,
                        'log' => $log,
                        'time' => $currentTime->toTimeString(),
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => "Waktu keluar siswa dicatat.",
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'message' => "kepulangan siswa sudah dicatat.",
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
