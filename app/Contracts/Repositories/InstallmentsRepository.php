<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\InstallmentsInterface;
use App\Models\PaymentInstallment;
use App\Services\PaymentService;
use Illuminate\Database\QueryException;

class InstallmentsRepository extends BaseRepository implements InstallmentsInterface
{

    private PaymentService $paymentService;

    public function __construct(PaymentInstallment $paymentInstallment, PaymentService $paymentService)
    {
        $this->model = $paymentInstallment;
        $this->paymentService = $paymentService;
    }

    public function show(mixed $id): mixed
    {
        return $this->model->query()->findOrFail($id);
    }

    public function get(): mixed
    {
        return $this->model->query()->with(['studentPayment.student', 'studentPayment.payment'])
            ->get();
    }

    public function store(array $data): mixed
    {
        $this->model->query()->create($data);
        return $this->paymentService->checkingPayment($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id): mixed
    {
        try {
            $data = $this->show($id);
            $data->delete($id);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) return false;
        }

        return true;
    }
}
