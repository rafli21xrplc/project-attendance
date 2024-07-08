<?php

namespace App\Services;

use App\Models\student;
use App\Traits\StudentTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentService
{
    use StudentTrait;

    private Model $student;
    private Model $user;

    public function __construct(student $student, User $user)
    {
        $this->student = $student;
        $this->user = $user;
    }

    public function store(array $data)
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'username' => $data['username'],
            'password'   => Hash::make($data['password']),
        ])->assignRole('student');

        return $this->responseStore($user, $data);
    }

    public function update(String $id, array $data): array
    {
        try {
            $student = $this->student->findOrFail($id);
            $user = $this->user->findOrFail($student->user_id);

            if (isset($data['password']) && $data['password']) {
                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            if (isset($data['username']) && $data['username']) {
                $user->update([
                    'username' => $data['username'],
                ]);
            }

            return $this->responseUpdate($user, $data);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Student or User not found');
        }
    }

    public function delete($id)
    {
        try {
            User::find($id)->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
