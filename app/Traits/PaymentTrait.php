<?php

namespace App\Traits;

use App\Models\classRoom;
use App\Models\payment;
use App\Models\student;
use App\Models\type_class;

trait PaymentTrait
{

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
}
