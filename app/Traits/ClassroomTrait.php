<?php


namespace App\Traits;

use App\Models\teacher;
use App\Models\type_class;

trait ClassroomTrait
{
        public function TypeClassroom()
        {
                return type_class::all();
        }

        public function getTeachers()
        {
                return teacher::all();
        }
}
