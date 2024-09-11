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
use App\Services\AttendanceHomeroomPdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Support\Facades\DB;
use ZipArchive;

trait AttendanceTrait
{

    private AttendanceHomeroomPdf $attendanceHomeroomPdf;

    public function getStudents()
    {
        return student::all();
    }

    public function generateAttendanceSummaryReport($classroom, $startDate, $endDate)
    {
        $attendanceSummary = [];

        foreach ($classroom->students as $student) {
            $attendanceSummary[$student->id] = $this->generateStudentAttendanceSummaryPdf($student->id, $startDate, $endDate, $this->getAbsencePoints());
        }

        return $attendanceSummary;
    }

    private function downloadZip($files, $month)
    {
        $zipFilename = 'attendance_reports_' . $month . '.zip';
        $zip = new ZipArchive();
        if ($zip->open(storage_path('app/public/' . $zipFilename), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Hapus file PDF individual setelah dimasukkan ke ZIP (opsional)
        foreach ($files as $file) {
            unlink($file);
        }

        return response()->download(storage_path('app/public/' . $zipFilename))->deleteFileAfterSend(true);
    }

    public function updateAttendanceStudent(array $attendances)
    {

        try {
            $updates = [];

            foreach ($attendances['attendance'] as $studentId => $status) {

                $existingAttendance = attendance::find($studentId);

                if (isset($existingAttendance)) {

                    if ($existingAttendance->status !== $status) {
                        $updates[] = [
                            'id' => $existingAttendance->id,
                            'status' => $status,
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            foreach ($updates as $update) {
                attendance::where('id', $update['id'])->update($update);
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function updateAttendanceStudentReport(array $attendances)
    {

        foreach ($attendances['attendance'] as $studentId => $status) {
            $existingAttendance = DB::select("
                SELECT * FROM attendance 
                WHERE id = ?
                LIMIT 1
            ", [$studentId]);

            if (!empty($existingAttendance) && $existingAttendance[0]->status !== $status) {
                DB::update("
                    UPDATE attendance 
                    SET status = ?, updated_at = NOW() 
                    WHERE id = ?
                ", [$status, $existingAttendance[0]->id]);
            }
        }

        try {


            return redirect()->back();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }



    public function getSchedules(array $data)
    {

        $dayOfWeek = Carbon::parse($data['date'])->format('l');

        $schedules = schedule::where('classroom_id', $data['classroom_id'])
            ->with(['classroom.students', 'course', 'teacher', 'StartTimeSchedules', 'EndTimeSchedules', 'attendances'])
            ->where('day_of_week', $dayOfWeek)
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

    public function AttendanceHomeroomReport($classroom_id, $month)
    {
        $monthCarbon = Carbon::create(Carbon::now()->year, $month, 1);

        $startDate = $monthCarbon->startOfMonth()->format('Y-m-d');
        $endDate = $monthCarbon->endOfMonth()->format('Y-m-d');

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

        $startDate = trim($data['start-date']);
        $endDate = trim($data['end-date']);

        $startDateObj = new DateTime($startDate);
        $endDateObj = new DateTime($endDate);

        $classroomId = classRoom::whereIn('type_class_id', $data['states'])
            ->orderByRaw("FIELD(name, 'METRO A', 'METRO B', 'ELIN A', 'ELIN B', 'RPL A', 'RPL B', 'RPL C', 'RPL D', 'TKJ A', 'TKJ B', 'TKJ C', 'TKJ D')")
            ->pluck('id');

        $report = $this->generateClassMonthlyReport($classroomId, $startDateObj, $endDateObj);

        $classrooms = Classroom::whereIn('id', $classroomId)->with('typeClass')->orderByRaw("FIELD(name, 'METRO A', 'METRO B', 'ELIN A', 'ELIN B', 'RPL A', 'RPL B', 'RPL C', 'RPL D', 'TKJ A', 'TKJ B', 'TKJ C', 'TKJ D')")->get();


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


    public function aggregateDailyAttendanceStudent($date, $studentId, $absencePoints)
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
            $status = $attendance->status;
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
            $summary[$status] += $attendance->hours;

            if ($status === 'alpha' && isset($absencePoints[$attendance->hours])) {
                $points = $absencePoints[$attendance->hours] * 7;
            }

            $times[$status][] = $attendance->id;
        }

        $statusString = '';
        $messageString = '';
        foreach ($summary as $status => $hours) {
            if ($hours > 0 && $status !== 'present') {
                $statusShort = $this->convertStatusToShortForm($status);
                $statusString .= "{$hours}{$statusShort}, ";

                $statusFull = $this->convertStatusToFullForm($status);
                $messageString .= "{$hours} Jam Pelajaran {$statusFull}, ";
            }
        }

        // Menghilangkan koma di akhir string
        $statusString = rtrim($statusString, ', ');
        $messageString = rtrim($messageString, ', ');

        if ($statusString === '') {
            $statusString = '10H';
            $messageString = '10 Jam Pelajaran Hadir';
        }

        return [
            'status' => $statusString,
            'messages' => $messageString,
        ];
    }

    private function convertStatusToFullForm($status)
    {
        switch ($status) {
            case 'alpha':
                return 'Alpha';
            case 'present':
                return 'Hadir';
            case 'permission':
                return 'Izin';
            case 'sick':
                return 'Sakit';
            default:
                return $status;
        }
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

            if ($status === 'alpha' && isset($absencePoints[$attendance->hours])) {
                $points = ($attendance->hours * 0.1) * 7;
            }

            $times[$status][] = $attendance->id;
        }

        $summaryString = '';
        foreach ($summary as $status => $hours) {
            if ($hours > 0 && $status !== 'present') {
                $statusShort = $this->convertStatusToShortForm($status);
                $summaryString .= "{$statusShort}{$hours}";
            }
        }

        if ($summaryString === '') {
            $summaryString = 'H10' === '' ? '' : $summaryString;
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

    public function generateAttendanceStudent($studentId)
    {
        $absencePoints = $this->getAbsencePoints();
        $attendanceSummary = $this->generateStudentAttendance($studentId, $absencePoints);
        return [
            'attendance' => $attendanceSummary['summary'],
        ];
    }

    public function generateClassMonthlyReport($classroomId, $startDate, $endDate)
    {

        $students = Student::whereIn('classroom_id', $classroomId)->with('classroom.typeClass')->orderBy('name', 'asc')->get();

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
                'total_points' => $attendanceSummary['total_alpha'] * 7,
                'warning' => $warning
            ];
        }

        return $report;
    }

    private function generateStudentAttendanceSummaryPdf($studentId, $startDate, $endDate, $absencePoints)
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
            $summary[$date->format('Y-m-d')] = $dailySummary['status'];
            $totalHadir += $dailySummary['summary']['present'];
            $totalIzin += $dailySummary['summary']['permission'];
            $totalSakit += $dailySummary['summary']['sick'];
            $totalAlpha += $dailySummary['summary']['alpha'];
            $totalPoints += $dailySummary['points'];

            if ($dailySummary['summary']['sick'] > 0) {
                $totalSickOccurrences++;
            }

            if ($dailySummary['summary']['permission'] > 0) {
                $totalPermissionOccurrences++;
            }
        }

        return [
            'summary' => $summary,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'total_points' => $totalPoints,
            'warning' => $totalPoints > 2.9 ? 'Parent Call' : ($totalPoints > 1.9 ? 'Student Call' : 'None')
        ];
    }

    public function generateStudentAttendance($studentId, $absencePoints)
    {
        $attendances = attendance::where('student_id', $studentId)
            ->orderBy('time', 'asc')
            ->get();

        $summary = [];


        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->time)->format('Y-m-d');
            $dailySummary = $this->aggregateDailyAttendanceStudent($date, $studentId, $absencePoints);
            $summary[$date] = [
                'summary' => [
                    $dailySummary['status'],
                    $dailySummary['messages'],
                ],
            ];
        }
        return [
            'summary' => $summary,
        ];
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
            $summary[$date->format('Y-m-d')] = [
                'status' => $dailySummary['status'],
                'times' => $dailySummary['times'],
                'summary' => $dailySummary['summary'],
                'points' => $dailySummary['points'],
            ];
            $totalHadir += $dailySummary['summary']['present'];
            $totalIzin += $dailySummary['summary']['permission'];
            $totalSakit += $dailySummary['summary']['sick'];
            $totalAlpha += $dailySummary['summary']['alpha'];
            $totalPoints = $dailySummary['points'];

            if ($dailySummary['summary']['sick'] > 0) {
                $totalSickOccurrences++;
            }

            if ($dailySummary['summary']['permission'] > 0) {
                $totalPermissionOccurrences++;
            }
        }

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

    private function getSchedule($studentId, $date)
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
            ->with(['StartTimeSchedules', 'EndTimeSchedules'])
            ->get();

        return $schedule;
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
