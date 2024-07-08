<?php


namespace App\Traits;

use App\Models\classRoom;
use App\Models\ExamLogin;
use App\Models\permission;
use App\Models\schedule;
use App\Models\student;
use App\Models\student_payment;
use App\Models\type_class;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait StudentTrait
{

        public function getScheduleStudent()
        {
                $student = Auth::user()->student;

                if (!$student) {
                        return redirect()->route('home')->with('error', 'Student not found.');
                }

                $today = Carbon::now()->format('l');

                $schedules = Schedule::where('classroom_id', $student->classroom_id)
                        ->where('day_of_week', $today)
                        ->with(['course', 'StartTimeSchedules', 'EndTimeSchedules'])
                        ->get();

                return $schedules;
        }

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
                list($typeClass, $className) = explode(' ', $row[3], 2);

                $typeClassModel = $this->findOrCreateTypeClass($typeClass);

                $classroom = $this->findOrCreateClassroom($className, $typeClassModel->id);

                $user = $this->createUser($row[1]);

                $this->createStudent($row, $classroom->id, $user->id);
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

        public function createStudent($row, $classroomId, $userId)
        {
                Student::create([
                        'id' => Str::uuid(),
                        'name' => $row[0],
                        'nisn' => $row[1],
                        'gender' => $row[2],
                        'classroom_id' => $classroomId,
                        'user_id' => $userId,
                ]);
        }


        public function getSchedule()
        {
                try {
                        $student = Auth::user()->student;
                        $today = Carbon::today();
                        $dayOfWeek = $today->format('l');

                        $schedules = schedule::where('classroom_id', $student->classroom_id)->with(['classroom', 'course', 'StartTimeSchedules', 'EndTimeSchedules'])
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
                $student = Auth::user()->student;

                try {
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

                        Permission::create([
                                'id' => Str::uuid(),
                                'student_id' => $student->id,
                                'file' => $filePath,
                                'description' => $data['description'] ?? null,
                        ]);
                } catch (\Throwable $th) {
                        throw new \Exception('Surat izin tidak bisa upload.');
                }
                return true;
        }

        public function responseStore(User $user, array $data): mixed
        {
                $data =
                        [
                                'nisn' => $data['username'],
                                'name' => $data['name'],
                                'gender' => $data['gender'],
                                'classroom_id' => $data['classroom_id'],
                                'day_of_birth' => $data['day_of_birth'],
                                'telp' => $data['telp'],
                                'user_id' => $user->id,
                        ];
                return $data;
        }

        private function responseUpdate(User $user, array $data): array
        {
                return [
                        'nisn' => $data['username'] ?? $user->username,
                        'name' => $data['name'] ?? $user->name,
                        'gender' => $data['gender'] ?? $user->gender,
                        'classroom_id' => $data['classroom_id'] ?? $user->classroom_id,
                        'day_of_birth' => $data['day_of_birth'] ?? $user->day_of_birth,
                        'telp' => $data['telp'] ?? $user->telp,
                        'user_id' => $user->id,
                ];
        }

        private function checkPaymentStudent()
        {
                $student = Auth::user()->student;

                $allPaymentsCompleted = student_payment::where('student_id', $student->id)->where('is_paid', false)->doesntExist();

                if ($allPaymentsCompleted) {

                        $examLogin = ExamLogin::where('student_id', $student->id)->first();

                        $qrContent = json_encode([
                                'username' => $examLogin->username,
                                'password' => $examLogin->password
                        ]);

                        $qrCode = QrCode::size(200)->generate(
                                $qrContent
                        );

                        return $qrCode;
                }
                return null;
        }
}
