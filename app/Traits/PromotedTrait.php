<?php


namespace App\Traits;

use App\Models\classRoom;
use App\Models\student;
use App\Models\student_class_history;
use App\Models\type_class;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait PromotedTrait
{

        public function get()
        {
                return student::with('classroom')->get();
        }

        public function getClassroom()
        {
                return classRoom::with('typeClass')->get();
        }

        public function promotedStudentAll()
        {
                $categories = type_class::orderBy('category')->pluck('category', 'id')->toArray();
                $today = Carbon::today();

                // Ambil semua kelas dan siswa
                $allClassrooms = Classroom::all()->groupBy('type_class_id');
                $allStudents = Student::all()->groupBy('classroom_id');

                foreach ($categories as $typeClassId => $category) {

                        if (!isset($allClassrooms[$typeClassId])) {
                                continue;
                        }

                        $classrooms = $allClassrooms[$typeClassId];

                        foreach ($classrooms as $classroom) {
                                // Ambil semua siswa di kelas ini
                                if (!isset($allStudents[$classroom->id])) {
                                        continue; // Jika tidak ada siswa di kelas ini, lewati
                                }
                                $students = $allStudents[$classroom->id];

                                foreach ($students as $student) {
                                        // Tentukan kategori berikutnya
                                        $categoryKeys = array_keys($categories);
                                        $currentCategoryIndex = array_search($typeClassId, $categoryKeys);
                                        $nextCategoryIndex = $currentCategoryIndex !== false ? $currentCategoryIndex + 1 : null;
                                        $nextCategoryId = $nextCategoryIndex !== null && $nextCategoryIndex < count($categoryKeys) ? $categoryKeys[$nextCategoryIndex] : null;

                                        if ($nextCategoryId) {
                                                $nextTypeClass = type_class::find($nextCategoryId);

                                                if ($nextTypeClass) {
                                                        // Cari atau buat kelas baru di type_class berikutnya dengan class_id yang sama
                                                        $nextClassroom = Classroom::where('name', $classroom->name)
                                                                ->where('type_class_id', $nextTypeClass->id)
                                                                ->first();

                                                        if (!$nextClassroom) {
                                                                // Jika kelas berikutnya tidak ditemukan, buat kelas baru
                                                                $nextClassroom = Classroom::create([
                                                                        'type_class_id' => $nextTypeClass->id,
                                                                        'class_id' => $classroom->class_id,
                                                                        'name' => $classroom->name,
                                                                        'teacher_id' => $classroom->teacher_id
                                                                ]);
                                                        }

                                                        // Update data siswa dengan classroom baru
                                                        $student->update([
                                                                'classroom_id' => $nextClassroom->id
                                                        ]);

                                                        // Tutup periode riwayat kelas lama
                                                        student_class_history::where('student_id', $student->id)
                                                                ->whereNull('end_date')
                                                                ->update(['end_date' => $today]);

                                                        // Tambahkan riwayat kelas baru
                                                        student_class_history::create([
                                                                'id' => Str::uuid(),
                                                                'student_id' => $student->id,
                                                                'classroom_id' => $nextClassroom->id,
                                                                'start_date' => $today
                                                        ]);
                                                }
                                        } else {
                                                // Jika tidak ada kategori berikutnya, siswa sudah di kelas terakhir (misalnya kelas XII)
                                                // Tandai siswa sebagai lulus (opsional jika ada kolom 'graduated')
                                                $student->update([
                                                        'graduated' => true
                                                ]);

                                                // Tutup periode riwayat kelas lama
                                                student_class_history::where('student_id', $student->id)
                                                        ->whereNull('end_date')
                                                        ->update(['end_date' => $today]);

                                                // Tambahkan riwayat kelas baru dengan status lulus
                                                student_class_history::create([
                                                        'id' => Str::uuid(),
                                                        'student_id' => $student->id,
                                                        'classroom_id' => $classroom->id,
                                                        'start_date' => $today,
                                                        'end_date' => $today // Tanggal kelulusan
                                                ]);
                                        }
                                }
                        }
                }
        }

        public function promotedStudent(array $data)
        {
                $student = Student::findOrFail($data['student_id']);
                $currentClassroom = Classroom::findOrFail($data['classroom_id']);
                $today = Carbon::today();

                student_class_history::where('student_id', $student->id)
                        ->whereNull('end_date')
                        ->update(['end_date' => $today]);

                $newClassroom = Classroom::where('name', $currentClassroom->name)->where('type_class_id', $data['type_class_id'])->first();

                $student->update([
                        'classroom_id' => $newClassroom->id,
                ]);

                student_class_history::create([
                        'id' => Str::uuid(),
                        'student_id' => $student->id,
                        'classroom_id' => $newClassroom->id,
                        'start_date' => $today,
                ]);
        }
}
