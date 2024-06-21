<?php


namespace App\Traits;

use App\Models\classRoom;
use App\Models\teacher;
use App\Models\type_class;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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

        public function importClassrooms(array $data)
        {
                $data = Excel::toArray([], $data['file']);

                DB::transaction(function () use ($data) {
                        foreach ($data[0] as $row) {
                                list($typeClass, $className) = explode(' ', $row[0], 2);

                                $typeClassModel = $this->findOrCreateTypeClass($typeClass);

                                $teacher = Teacher::where('name', $row[1])->first();

                                if ($teacher) {
                                        $teacher_id = $teacher->id;

                                        // Create classroom
                                        ClassRoom::create([
                                                'id' => Str::uuid(),
                                                'type_class_id' => $typeClassModel->id,
                                                'class_id' => Str::uuid(),
                                                'name' => $className,
                                                'teacher_id' => $teacher_id,
                                        ]);
                                }
                        }
                });

                return back();
        }

        public function findOrCreateTypeClass($typeClass)
        {
                $typeClassModel = type_class::where('category', $typeClass)->first();

                if (!$typeClassModel) {
                        $typeClassModel = type_class::create([
                                'id' => Str::uuid(),
                                'category' => $typeClass
                        ]);
                }

                return $typeClassModel;
        }
}
