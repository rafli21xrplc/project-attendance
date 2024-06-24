<?php


namespace App\Traits;

use App\Models\absence_point;
use App\Models\attendance;
use Illuminate\Support\Str;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\student;
use App\Models\time_schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

trait AttendanceTrait
{

    public function updateAttendanceStudent(array $attendances, $id)
    {
        try {

            $schedule = schedule::findOrFail($id);
            $currentPeriod = kbm_period::getCurrentPeriod();

            $startSchedule = time_schedule::findOrFail($schedule->start_time_schedule_id);
            $endSchedule = time_schedule::findOrFail($schedule->end_time_schedule_id);

            $hours = ($endSchedule->time_number - $startSchedule->time_number) + 1;

            foreach ($attendances['attendance'] as $studentId => $status) {
                $existingAttendance = attendance::where('student_id', $studentId)
                    ->where('schedule_id', $id)
                    ->first();

                if ($existingAttendance) {
                    $existingAttendance->update([
                        'time' => now()->format('Y-m-d H:i:s'),
                        'status' => $status,
                        'hours' => $hours,
                        'kbm_period_id' => $currentPeriod->id,
                        'updated_at' => now(),
                    ]);
                }
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function getSchedules(array $data)
    {

        $schedules = schedule::where('classroom_id', $data['classroom_id'])
            ->whereDate('created_at', $data['date'])
            ->with(['classroom.students', 'course', 'teacher', 'StartTimeSchedules', 'EndTimeSchedules', 'attendances'])
            ->get();

        $attendanceBySchedule = [];
        foreach ($schedules as $schedule) {
            foreach ($schedule->classroom->students as $student) {
                if (!isset($attendanceBySchedule[$schedule->id][$student->id])) {
                    $attendanceBySchedule[$schedule->id][$student->id] = [
                        'student_id' => $student->id,
                        'name' => $student->name,
                        'attendance_status' => [],
                    ];
                }
                $attendance = $schedule->attendances->firstWhere('student_id', $student->id);
                $attendanceBySchedule[$schedule->id][$student->id]['attendance_status'][] = $attendance ? $attendance->status : null;
            }
        }

        return $schedules;
    }

    public function getSchedulesDefault()
    {

        $classroom = classRoom::first();
        $date = Carbon::now()->format('Y-m-d');

        $schedules = schedule::where('classroom_id', $classroom->id)
            ->whereDate('created_at', $date)
            ->with(['classroom.students', 'course', 'teacher', 'StartTimeSchedules', 'EndTimeSchedules', 'attendances'])
            ->get();

        $attendanceBySchedule = [];
        foreach ($schedules as $schedule) {
            foreach ($schedule->classroom->students as $student) {
                if (!isset($attendanceBySchedule[$schedule->id][$student->id])) {
                    $attendanceBySchedule[$schedule->id][$student->id] = [
                        'student_id' => $student->id,
                        'name' => $student->name,
                        'attendance_status' => [],
                    ];
                }
                $attendance = $schedule->attendances->firstWhere('student_id', $student->id);
                $attendanceBySchedule[$schedule->id][$student->id]['attendance_status'][] = $attendance ? $attendance->status : null;
            }
        }

        return $schedules;
    }

    public function getAttendance()
    {
        return attendance::with(['student', 'schedule.classroom.students'])->first();
    }

    public function getClassrooms()
    {
        return classRoom::with('typeClass')->get();
    }

    public function AttendanceHomeroom($classroom_id)
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        $startDateObj = new DateTime($startDate);
        $endDateObj = new DateTime($endDate);

        $report = $this->generateClassMonthlyReport([$classroom_id], $startDateObj, $endDateObj);

        $classrooms = Classroom::where('id', $classroom_id)->with('typeClass')->first();

        return [
            'report' => $report,
            'classroom' => $classrooms,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    public function searchAttendance($data)
    {
        $dates = explode("to", $data['range-date']);
        $startDate = trim($dates[0]);
        $endDate = trim($dates[1]);

        $startDateObj = new DateTime($startDate);
        $endDateObj = new DateTime($endDate);

        $report = $this->generateClassMonthlyReport($data['states'], $startDateObj, $endDateObj);

        $classrooms = Classroom::whereIn('id', $data['states'])->with('typeClass')->get();

        return [
            'report' => $report,
            'classroom' => $classrooms,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    public function getAbsencePoints()
    {
        return absence_point::all()->keyBy('hours_absent')->map(function ($item) {
            return $item->points;
        });
    }


    public function aggregateDailyAttendance($date, $studentId, $absencePoints)
    {
        $attendances = attendance::whereDate('time', $date)
            ->where('student_id', $studentId)
            ->get();

        $summary = [
            'present' => 0,
            'permission' => 0,
            'sick' => 0,
            'alpha' => 0,
        ];
        $times = [];
        $points = 0;

        foreach ($attendances as $attendance) {
            $status = $attendance->status;
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
            $summary[$status] += $attendance->hours;

            // Calculate points based on absence points table
            if ($status !== 'present' && isset($absencePoints[$attendance->hours])) {
                $points += $absencePoints[$attendance->hours];
            }

            $times[$status][] = $attendance->id;
        }

        $summaryString = '';
        foreach ($summary as $status => $hours) {
            if ($hours > 0) {
                $statusShort = $this->convertStatusToShortForm($status);
                $summaryString .= "{$hours}{$statusShort}";
            }
        }

        return [
            'status' => $summaryString,
            'times' => $times,
            'summary' => $summary,
            'points' => $points
        ];
    }

    private function convertStatusToShortForm($status)
    {
        switch ($status) {
            case 'alpha':
                return 'A';
            case 'present':
                return 'H';
            case 'permission':
                return 'I';
            case 'sick':
                return 'S';
            default:
                return $status;
        }
    }

    public function generateClassMonthlyReport($classroomId, $startDate, $endDate)
    {
        $students = Student::whereIn('classroom_id', $classroomId)->with('classroom.typeClass')->get();
        $absencePoints = $this->getAbsencePoints();

        $report = [];

        foreach ($students as $student) {
            $attendanceSummary = $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate, $absencePoints);
            $totalPoints = $attendanceSummary['total_points'];
            $warning = $totalPoints > 2.9 ? 'panggilan walimurid' : ($totalPoints > 1.9 ? 'konfirmasi' : ' - ');

            $report[$student->id] = [
                'nisn' => $student->id,
                'name' => $student->name,
                'gender' => $student->gender,
                'class' => $student->classroom->typeClass->category . " " . $student->classroom->name,
                'attendance' => $attendanceSummary['summary'],
                'total_hadir' => $attendanceSummary['total_hadir'],
                'total_izin' => $attendanceSummary['total_izin'],
                'total_sakit' => $attendanceSummary['total_sakit'],
                'total_alpha' => $attendanceSummary['total_alpha'],
                'total_points' => $totalPoints,
                'warning' => $warning
            ];
        }

        return $report;
    }


    public function generateStudentAttendanceSummary($studentId, $startDate, $endDate, $absencePoints)
    {
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $summary = [];
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpha = 0;
        $totalPoints = 0;
        $totalSickOccurrences = 0;
        $totalPermissionOccurrences = 0;

        foreach ($dateRange as $date) {
            $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $studentId, $absencePoints);
            $summary[$date->format('Y-m-d')] = $dailySummary;
            $totalHadir += $dailySummary['summary']['present'];
            $totalIzin += $dailySummary['summary']['permission'];
            $totalSakit += $dailySummary['summary']['sick'];
            $totalAlpha += $dailySummary['summary']['alpha'];

            if ($dailySummary['summary']['sick'] > 0) {
                $totalSickOccurrences++;
            }

            if ($dailySummary['summary']['permission'] > 0) {
                $totalPermissionOccurrences++;
            }
        }

        if ($totalSickOccurrences >= 8) {
            $totalPoints += 0.5;
        }

        if ($totalPermissionOccurrences >= 8) {
            $totalPoints += 0.5;
        }

        $totalPoints += ($totalAlpha * 0.1);

        return [
            'summary' => $summary,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'total_points' => $totalPoints
        ];
    }

    public function getAttendanceData($studentId, $date)
    {
        return Attendance::where('student_id', $studentId)
            ->whereDate('time', $date)
            ->get();
    }


    public function parseInput($input)
    {
        preg_match_all('/(\d+)(\w)/', $input, $matches, PREG_SET_ORDER);
        $statusCounts = [];
        foreach ($matches as $match) {
            $hours = (int)$match[1];
            $status = $this->convertFormToShortStatus($match[2]);
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status] += $hours;
        }
        return $statusCounts;
    }

    public function determineChanges($existingData, $newStatusCounts)
    {
        $existingStatusCounts = [];
        $recordsToUpdate = [];
        $recordsToCreate = [];
        $recordsToDelete = [];

        foreach ($existingData as $record) {
            $status = $record->status;
            $hours = $record->hours;
            if (!isset($existingStatusCounts[$status])) {
                $existingStatusCounts[$status] = 0;
            }
            $existingStatusCounts[$status] += $hours;
        }

        foreach ($existingStatusCounts as $status => $hours) {
            if (isset($newStatusCounts[$status])) {
                if ($hours != $newStatusCounts[$status]) {
                    $recordsToUpdate[$status] = [
                        'old_hours' => $hours,
                        'new_hours' => $newStatusCounts[$status]
                    ];
                }
            } else {
                $recordsToDelete[$status] = $hours;
            }
        }

        foreach ($newStatusCounts as $status => $hours) {
            if (!isset($existingStatusCounts[$status])) {
                $recordsToCreate[$status] = $hours;
            }
        }

        return [
            'update' => $recordsToUpdate,
            'create' => $recordsToCreate,
            'delete' => $recordsToDelete
        ];
    }

    public function applyChanges($studentId, $date, $changes)
    {
        $currentPeriod = kbm_period::getCurrentPeriod();

        if (!$currentPeriod) {
            return response()->json(['error' => 'No active KBM period found.'], 404);
        }

        foreach ($changes['update'] as $status => $data) {
            $attendance = attendance::where('student_id', $studentId)
                ->whereDate('time', $date)
                ->where('status', $status)
                ->first();
            if ($attendance) {
                $attendance->hours = $data['new_hours'];
                $attendance->save();
            }
        }

        foreach ($changes['create'] as $status => $hours) {
            $originalAttendance = attendance::where('student_id', $studentId)
                ->whereDate('time', $date)
                ->first();

            attendance::insert([
                'id' => Str::uuid(),
                'student_id' => $studentId,
                'schedule_id' => $originalAttendance ? $originalAttendance->schedule_id : $this->getScheduleId($studentId, $date),
                'kbm_period_id' => $currentPeriod->id,
                'time' => $originalAttendance ? $originalAttendance->time : Carbon::parse($date)->format('Y-m-d'),
                'status' => $status,
                'hours' => $hours,
                'created_at' => now(),
            ]);
        }

        // Step 3: Delete records
        foreach ($changes['delete'] as $status => $hours) {
            attendance::where('student_id', $studentId)
                ->whereDate('time', $date)
                ->where('status', $status)
                ->delete();
        }

        return response()->json(['success' => true]);
    }



    // public function determineChanges($existingData, $newInput)
    // {
    //     $totalHours = 0;
    //     $statusCounts = [];
    //     $recordsToUpdate = [];
    //     $recordsToCreate = [];
    //     $recordsToDelete = [];

    //     foreach ($existingData as $record) {
    //         $totalHours += $record->hours;
    //         $status = $record->status;
    //         if (!isset($statusCounts[$status])) {
    //             $statusCounts[$status] = 0;
    //         }
    //         $statusCounts[$status] += $record->hours;
    //     }

    //     // Parse new input
    //     preg_match_all('/(\d+)(\w)/', $newInput, $matches, PREG_SET_ORDER);
    //     $newStatusCounts = [];
    //     foreach ($matches as $match) {
    //         $hours = (int)$match[1];
    //         $status = $match[2];
    //         if (!isset($newStatusCounts[$status])) {
    //             $newStatusCounts[$status] = 0;
    //         }
    //         $newStatusCounts[$status] += $hours;
    //     }

    //     // Determine changes
    //     foreach ($statusCounts as $status => $hours) {
    //         if (isset($newStatusCounts[$status])) {
    //             if ($hours != $newStatusCounts[$status]) {
    //                 $recordsToUpdate[$status] = [
    //                     'old_hours' => $hours,
    //                     'new_hours' => $newStatusCounts[$status]
    //                 ];
    //             }
    //         } else {
    //             $recordsToDelete[$status] = $hours;
    //         }
    //     }

    //     foreach ($newStatusCounts as $status => $hours) {
    //         if (!isset($statusCounts[$status])) {
    //             $recordsToCreate[$status] = $hours;
    //         }
    //     }

    //     return [
    //         'update' => $recordsToUpdate,
    //         'create' => $recordsToCreate,
    //         'delete' => $recordsToDelete
    //     ];
    // }

    // public function updateAttendance($studentId, $date, $changes)
    // {
    //     $currentPeriod = kbm_period::getCurrentPeriod();

    //     if (!$currentPeriod) {
    //         return response()->json(['error' => 'No active KBM period found.'], 404);
    //     }

    //     // Step 1: Update existing records
    //     foreach ($changes['update'] as $status => $data) {
    //         $attendance = Attendance::where('student_id', $studentId)
    //             ->whereDate('time', $date)
    //             ->where('status', $status)
    //             ->first();
    //         if ($attendance) {
    //             $attendance->hours = $data['new_hours'];
    //             $attendance->save();
    //         }
    //     }

    //     // Step 2: Create new records
    //     foreach ($changes['create'] as $status => $hours) {
    //         $originalAttendance = Attendance::where('student_id', $studentId)
    //             ->whereDate('time', $date)
    //             ->first();

    //         Attendance::insert([
    //             'id' => Str::uuid(),
    //             'student_id' => $studentId,
    //             'schedule_id' => $originalAttendance ? $originalAttendance->schedule_id : $this->getScheduleId($studentId, $date),
    //             'kbm_period_id' => $currentPeriod->id,
    //             'time' => $originalAttendance ? $originalAttendance->time : now()->format('Y-m-d H:i:s'),
    //             'status' => $this->convertFormToShortStatus($status),
    //             'hours' => $hours,
    //             'created_at' => now(),
    //         ]);
    //     }

    //     // Step 3: Delete records
    //     foreach ($changes['delete'] as $status => $hours) {
    //         attendance::where('student_id', $studentId)
    //             ->whereDate('time', $date)
    //             ->where('status', $status)
    //             ->delete();
    //         // if ($attendance) {
    //         //     $attendance->delete();
    //         // }
    //     }

    //     return response()->json(['success' => true]);
    // }



    private function getOriginalTime($studentId, $date)
    {
        $attendance = Attendance::where('student_id', $studentId)
            ->whereDate('time', $date)
            ->first();

        return $attendance;
    }

    private function getScheduleId($studentId, $date)
    {
        $student = student::find($studentId);
        if (!$student) {
            return null;
        }

        $classroomId = $student->classroom_id;
        $carbonDate = Carbon::parse($date);
        $dayName = $carbonDate->format('l');


        $schedule = schedule::where('classroom_id', $classroomId)
            ->where('day_of_week', $dayName)
            ->first();

        return $schedule ? $schedule->id : null;
    }

    private function convertFormToShortStatus($status)
    {
        switch ($status) {
            case 'A':
                return 'alpha';
            case 'H':
                return 'present';
            case 'I':
                return 'permission';
            case 'S':
                return 'sick';
            default:
                return $status;
        }
    }
}
