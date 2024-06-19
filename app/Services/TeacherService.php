<?php

namespace App\Services;

use App\Models\student;
use App\Models\teacher;
use App\Traits\TeacherTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeacherService
{

    use TeacherTrait;

    private Model $teacher;
    private Model $user;

    public function __construct(teacher $teacher, User $user)
    {
        $this->teacher = $teacher;
        $this->user = $user;
    }


    public function store(array $data)
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'username' => $data['username'],
            'password'   => Hash::make($data['password']),
        ])->assignRole('teacher');

        return $this->responseStore($user, $data);
    }

    public function update(String $id, array $data): array
    {
        try {
            $teacher = $this->teacher->findOrFail($id);
            $user = $this->user->findOrFail($teacher->user_id);

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
