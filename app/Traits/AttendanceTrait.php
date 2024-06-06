<?php


namespace App\Traits;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\schedule;
use App\Models\student;
use App\Models\time_schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use DateTime;

trait AttendanceTrait
{

    public function updateAttendanceStudent(array $attendances, $id)
    {
        try {
            $schedule = schedule::findOrFail($id);

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
                        'updated_at' => now(),
                    ]);
                }
            }

            // $schedule->update([
            //     'completed' => true
            // ]);

            return redirect()->back();
        } catch (\Throwable $th) {
            return false;
        }
    }


    public function getSchedules(array $data)
    {
        // $schedules = schedule::where('classroom_id', $data['classroom_id'])
        //     ->whereDate('created_at', $data['date'])
        //     ->with(['classroom.students', 'course', 'teacher', 'StartTimeSchedules', 'EndTimeSchedules', 'attendances'])
        //     ->get();

        // $attendanceBySchedule = [];

        // foreach ($schedules as $schedule) {
        //     foreach ($schedule->classroom->students as $student) {
        //         if (!isset($attendanceBySchedule[$schedule->id][$student->id])) {
        //             $attendanceBySchedule[$schedule->id][$student->id] = [
        //                 'student_id' => $student->id,
        //                 'name' => $student->name,
        //                 'attendance_status' => [],
        //             ];
        //         }

        //         $attendance = $schedule->attendances->firstWhere('student_id', $student->id);

        //         $attendanceBySchedule[$schedule->id][$student->id]['attendance_status'][] = $attendance ? $attendance->status : null;
        //     }
        // }

        // return $schedules;

        // $schedules = schedule::where('classroom_id', $data['classroom_id'])
        //     ->whereDate('created_at', $data['date'])
        //     ->with(['classroom.students', 'course', 'teacher', 'StartTimeSchedules', 'EndTimeSchedules', 'attendances'])
        //     ->get();



        // return $schedules;

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
                        'attendance_status' => [], // Initialize attendance_status array
                    ];
                }
                $attendance = $schedule->attendances->firstWhere('student_id', $student->id);
                // Push attendance status to the array
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
        return classRoom::all();
    }

    public function searchAttendance($data)
    {
        $dates = explode("to", $data['range-date']);
        $startDate = trim($dates[0]);
        $endDate = trim($dates[1]);

        $startDateObj = new DateTime($startDate);
        $endDateObj = new DateTime($endDate);

        $report = $this->generateClassMonthlyReport($data['classroom_id'], $startDateObj, $endDateObj);

        $classrooms = Classroom::whereIn('id', ["5ea177d1-2aa7-330e-8500-7a354e96c82a", "91c1ba44-ca9b-34d7-92e5-6bdf21b4def0"])->get();

        // dd($classrooms);

        // return view('admin.attendance_report', compact('report', 'startDate', 'endDate', 'classrooms'));

        // $classroom = classRoom::find($data['classroom_id']);

        return [
            'report' => $report,
            'classroom' => $classrooms,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    public function aggregateDailyAttendance($date, $studentId)
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

        foreach ($attendances as $attendance) {
            $summary[$attendance->status] += $attendance->hours;
        }

        $summaryString = '';
        if ($summary['permission'] > 0) {
            $summaryString .= "{$summary['permission']}i";
        }
        if ($summary['present'] > 0) {
            $summaryString .= "{$summary['present']}H";
        }
        if ($summary['sick'] > 0) {
            $summaryString .= "{$summary['sick']}S";
        }
        if ($summary['alpha'] > 0) {
            $summaryString .= "{$summary['alpha']}A";
        }

        return $summaryString;
    }


    public function generateClassMonthlyReport($classroomId, $startDate, $endDate)
    {

        $students = Student::whereIn('classroom_id', ["5ea177d1-2aa7-330e-8500-7a354e96c82a", "91c1ba44-ca9b-34d7-92e5-6bdf21b4def0"])->get();
        $report = [];

        foreach ($students as $student) {
            $report[$student->id] = [
                'name' => $student->name,
                'class' => $student->classroom->name,
                'attendance' => $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate)
            ];
        }


        return $report;


        // $students = student::where('classroom_id', $classroomId)->get();
        // $report = [];

        // foreach ($students as $student) {
        //     $report[$student->id] = [
        //         'name' => $student->name,
        //         'attendance' => $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate)
        //     ];
        // }

        // return $report;
    }

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
