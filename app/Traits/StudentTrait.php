<?php


namespace App\Traits;

use App\Models\classRoom;
use App\Models\permission;
use App\Models\schedule;
use App\Models\student;
use App\Models\type_class;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait StudentTrait
{

        public function importStudents(array $excel)
        {
                $data = Excel::toArray([], $excel['file']);
                DB::transaction(function () use ($data) {
                        foreach ($data[0] as $row) {
                                $this->processRow($row);
                        }
                });

                return back();
        }

        public function processRow($row)
        {
                $dayOfBirth = $this->convertExcelDate($row[2]);

                list($typeClass, $className) = explode(' ', $row[1], 2);

                $typeClassModel = $this->findOrCreateTypeClass($typeClass);

                $classroom = $this->findOrCreateClassroom($className, $typeClassModel->id);

                $user = $this->createUser($row[3]);

                $this->createStudent($row, $classroom->id, $user->id, $dayOfBirth);
        }

        public function convertExcelDate($excelDate)
        {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate))->format('Y-m-d');
        }

        public function findOrCreateTypeClass($typeClass)
        {
                $typeClassModel = type_class::where('category', $typeClass)->first();

                if (!$typeClassModel) {
                        $typeClassModel = type_class::create([
                                'id' => Str::uuid(),
                                'category' => $typeClass
                        ]);
                }

                return $typeClassModel;
        }

        public function findOrCreateClassroom($className, $typeClassId)
        {
                $classroom = Classroom::where('name', $className)
                        ->where('type_class_id', $typeClassId)
                        ->first();

                if (!$classroom) {
                        $classroom = Classroom::create([
                                'name' => $className,
                                'type_class_id' => $typeClassId
                        ]);
                }

                return $classroom;
        }

        public function createUser($username)
        {
                return User::create([
                        'uuid' => Str::uuid(),
                        'username' => $username,
                        'password' => Hash::make('password')
                ])->assignRole('student');
        }

        public function createStudent($row, $classroomId, $userId, $dayOfBirth)
        {
                Student::create([
                        'id' => Str::uuid(),
                        'name' => $row[6],
                        'gender' => $row[4],
                        'classroom_id' => $classroomId,
                        'day_of_birth' => $dayOfBirth,
                        'telp' => $row[5],
                        'user_id' => $userId,
                ]);
        }


        public function getSchedule()
        {
                try {
                        $student = Auth::user()->student;
                        $today = Carbon::today();
                        $dayOfWeek = $today->format('l');

                        $schedules = schedule::where('classroom_id', $student->classroom_id)
                                ->where('day_of_week', $dayOfWeek)
                                ->get();

                        return $schedules;
                } catch (\Throwable $th) {
                        return collect();
                }
        }

        public function storePermission(array $data)
        {
                $today = Carbon::today();
                $dayOfWeek = $today->format('l');
                $student = Auth::user()->student;

                $schedules = Schedule::where('classroom_id', $student->classroom_id)
                        ->where('day_of_week', $dayOfWeek)
                        ->get();

                $existingPermission = Permission::where('student_id', $student->id)
                        ->whereDate('created_at', $today)
                        ->first();

                if ($existingPermission) {
                        throw new \Exception('Surat izin untuk hari ini sudah diajukan.');
                }

                if (isset($data['file'])) {
                        $filePath = $data['file']->store('images/permission', 'public');
                } else {
                        throw new \Exception('File surat izin diperlukan.');
                }

                foreach ($schedules as $schedule) {
                        Permission::create([
                                'id' => Str::uuid(),
                                'student_id' => $student->id,
                                'schedule_id' => $schedule->id,
                                'file' => $filePath,
                                'description' => $data['description'] ?? null,
                        ]);
                }

                return true;
        }

        public function responseStore(User $user, array $data): mixed
        {
                $data =
                        [
                                'name' => $data['name'],
                                'gender' => $data['gender'],
                                // 'address' => $data['address'],
                                'classroom_id' => $data['classroom_id'],
                                // 'religion_id' => $data['religion_id'],
                                // 'born_at' => $data['born_at'],
                                'day_of_birth' => $data['day_of_birth'],
                                'telp' => $data['telp'],
                                'user_id' => $user->id,
                        ];
                return $data;
        }

        private function responseUpdate(User $user, array $data): array
        {
                return [
                        'name' => $data['name'] ?? $user->name,
                        'gender' => $data['gender'] ?? $user->gender,
                        // 'address' => $data['address'] ?? $user->address,
                        'classroom_id' => $data['classroom_id'] ?? $user->classroom_id,
                        // 'religion_id' => $data['religion_id'] ?? $user->religion_id,
                        // 'born_at' => $data['born_at'] ?? $user->born_at,
                        'day_of_birth' => $data['day_of_birth'] ?? $user->day_of_birth,
                        'telp' => $data['telp'] ?? $user->telp,
                        'user_id' => $user->id,
                ];
        }
}
