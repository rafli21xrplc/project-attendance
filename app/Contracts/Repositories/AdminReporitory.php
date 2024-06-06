<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\AdminInterface;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Database\QueryException;

class AdminReporitory extends BaseRepository implements AdminInterface
{
    public function __construct(User $user, AdminService $adminService)
    {
        $this->model = $user;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()
            ->get();
    }

    public function store(array $data): mixed
    {
        return $this->model->query()->create($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id): mixed
    {
        try {
            $this->show($id)->delete($id);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) return false;
        }
        return true;
    }
}
