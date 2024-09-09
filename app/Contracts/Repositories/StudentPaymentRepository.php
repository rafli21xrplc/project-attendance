<?php

namespace App\Contracts\Repositories;
use App\Contracts\Interfaces\StudentPaymentInterface;
use App\Models\student_payment;
use App\Traits\PaymentTrait;
use Illuminate\Database\QueryException;

class StudentPaymentRepository extends BaseRepository implements StudentPaymentInterface
{
    use PaymentTrait;
    public function __construct(student_payment $student_payment)
    {
        $this->model = $student_payment;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function getTypeClassroom(): mixed
    {
        return $this->getTypeClassrooms();
    }

    public function getClassroom(): mixed
    {
        return $this->getClassrooms();
    }

    public function getStudent(): mixed
    {
        return $this->getStudents();
    }

    public function getPayment(): mixed
    {
        return $this->getPayments();
    }

    public function getTypePayment(): mixed
    {
        return $this->getTypePayments();
    }

    public function get(): mixed
    {
        return student_payment::getStudentPayments();
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
