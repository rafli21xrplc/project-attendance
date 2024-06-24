<?php


namespace App\Traits;

use App\Models\classRoom;
use App\Models\student;
use Carbon\Carbon;

trait PromotedTrait
{

        public function promotedStudent()
        {
                $currentClassrooms = Classroom::with('typeClass')->get();

                foreach ($currentClassrooms as $classroom) {
                        $newClassroom = null;

                        switch ($classroom->typeClass->category) {
                                case 'X':
                                        $newClassroom = Classroom::whereHas('typeClass', function ($query) {
                                                $query->where('category', 'XI');
                                        })->first();
                                        break;
                                case 'XI':
                                        $newClassroom = Classroom::whereHas('typeClass', function ($query) {
                                                $query->where('category', 'XII');
                                        })->first();
                                        break;
                                case 'XII':
                                        $newClassroom = Classroom::whereHas('typeClass', function ($query) {
                                                $query->where('category', 'XIII');
                                        })->first();
                                        break;
                                default:
                                        continue 2;
                        }

                        if ($newClassroom) {
                                Student::where('classroom_id', $classroom->id)->update(['classroom_id' => $newClassroom->id]);
                        }
                }
        }
}
