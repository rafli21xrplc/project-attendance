<?php

namespace App\Traits;

use App\Http\Controllers\Admin\installments_payment;
use App\Models\classRoom;
use App\Models\payment;
use App\Models\PaymentInstallment;
use App\Models\student;
use App\Models\student_payment;
use App\Models\type_class;
use App\Models\type_payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait PaymentTrait
{
        public function getPayment()
        {
                return payment::all();
        }
        public function getTypePayments()
        {
                return type_payment::all();
        }
        public function getTypeClassrooms()
        {
                return type_class::with('classrooms')->get();
        }
        public function getClassrooms()
        {
                return classRoom::all();
        }
        public function getStudents()
        {
                return student::all();
        }
        public function getPayments()
        {
                return payment::all();
        }
        public function storeStudentPayment(array $data)
        {
                try {
                        foreach ($data['selected_student_ids'] as $studentId) {
                                student_payment::create([
                                        'id' => Str::uuid(),
                                        'student_id' => $studentId,
                                        'payment_id' => $data['payment_id'],
                                        'is_paid' => 0,
                                        'payment_date' => now(),
                                ]);
                        }

                        return true;
                } catch (\Throwable $th) {
                        return $th->getMessage();
                }
        }

        public function getInstallments()
        {
                return PaymentInstallment::with('studentPayment')->get();
        }

        public function getTagihanSiswa()
        {
                return student_payment::getStudentPaymentsInstallments();
        }

        public function importPayment(array $excel)
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
                return payment::created([
                        'id' => Str::uuid(),
                        'name' => $row[0],
                        'amount' => $row[1],
                        'tenggat' => $row[2],
                ]);
        }
}
