<?php


namespace App\Traits;

use App\Models\permission;
use App\Models\schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait StudentTrait
{

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
