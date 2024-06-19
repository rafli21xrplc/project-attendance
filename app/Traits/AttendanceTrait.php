<?php


namespace App\Traits;

use App\Models\attendance;
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

    // public function aggregateDailyAttendance($date, $studentId)
    // {
    //     $attendances = attendance::whereDate('time', $date)
    //         ->where('student_id', $studentId)
    //         ->get();

    //     $summary = [
    //         'present' => 0,
    //         'permission' => 0,
    //         'sick' => 0,
    //         'alpha' => 0,
    //     ];

    //     foreach ($attendances as $attendance) {
    //         $summary[$attendance->status] += $attendance->hours;
    //     }

    //     $summaryString = '';
    //     if ($summary['permission'] > 0) {
    //         $summaryString .= "{$summary['permission']}i";
    //     }
    //     if ($summary['present'] > 0) {
    //         $summaryString .= "{$summary['present']}H";
    //     }
    //     if ($summary['sick'] > 0) {
    //         $summaryString .= "{$summary['sick']}S";
    //     }
    //     if ($summary['alpha'] > 0) {
    //         $summaryString .= "{$summary['alpha']}A";
    //     }

    //     return $summaryString;
    // }

    public function aggregateDailyAttendance($date, $studentId)
    {
        $attendances = attendance::whereDate('time', $date)
            ->where('student_id', $studentId)
            ->get();

        $summary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        $times = [];

        foreach ($attendances as $attendance) {
            $status = $attendance->status;
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
            $summary[$status] += $attendance->hours;

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
            'times' => $times
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
            case 'sakit':
                return 'S';
            default:
                return $status;
        }
    }


    public function generateClassMonthlyReport($classroomId, $startDate, $endDate)
    {
        $students = Student::whereIn('classroom_id', $classroomId)->with('classroom.typeClass')->get();

        $report = [];

        foreach ($students as $student) {
            $report[$student->id] = [
                'name' => $student->name,
                'class' => $student->classroom->typeClass->category . " " . $student->classroom->name,
                'attendance' => $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate)
            ];
        }

        return $report;
    }


    // public function generateStudentAttendanceSummary($studentId, $startDate, $endDate)
    // {
    //     $dateRange = CarbonPeriod::create($startDate, $endDate);
    //     $summary = [];

    //     foreach ($dateRange as $date) {
    //         $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $studentId);
    //         $summary[$date->format('Y-m-d')] = $dailySummary;
    //     }

    //     return $summary;
    // }

    public function generateStudentAttendanceSummary($studentId, $startDate, $endDate)
    {
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $summary = [];

        foreach ($dateRange as $date) {
            $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $studentId);
            $summary[$date->format('Y-m-d')] = $dailySummary;
        }

        return $summary;
    }
}
