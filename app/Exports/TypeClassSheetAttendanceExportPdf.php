<?php

namespace App\Exports;

use App\Models\absence_point;
use App\Models\attendance;
use App\Models\type_class;
use Carbon\CarbonPeriod;

class TypeClassSheetAttendanceExportPdf
{
    protected $typeClass;
    protected $startDate;
    protected $endDate;

    public function __construct($typeClass, $startDate, $endDate)
    {
        $this->typeClass = $typeClass;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = [];
        $absencePoints = $this->getAbsencePoints();
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($this->typeClass->classrooms as $classroom) {
            $classroomData = [];
            $classroomData[] = ['Kelas:', $classroom->typeClass->category . ' ' . $classroom->name];
            $classroomData[] = ['Wali Kelas:', $classroom->teacher->name ?? 'N/A'];
            $classroomData[] = ['Tanggal Rekap:', date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate))];

            $classroomData[] = [''];

            $header = ['NO', 'NISN', 'NAMA SISWA', 'L/P'];
            foreach ($dateRange as $date) {
                $header[] = $date->format('d');
            }
            $header[] = 'S';
            $header[] = 'I';
            $header[] = 'A';
            $header[] = 'POIN TATIB';
            $header[] = 'KET';

            $classroomData[] = $header;

            $attendanceSummary = [];
            foreach ($classroom->students as $student) {
                $attendanceSummary[$student->id] = $this->generateStudentAttendanceSummary($student->id, $this->startDate, $this->endDate, $absencePoints);
            }

            $rowNumber = 1;
            foreach ($classroom->students as $student) {
                $row = [
                    $rowNumber++,
                    $student->student_id,
                    $student->name,
                    $student->gender
                ];

                foreach ($dateRange as $date) {
                    $status = $attendanceSummary[$student->id]['summary'][$date->format('Y-m-d')] ?? '';
                    // Leave column empty if student is present for all hours
                    if ($status == 'H') {
                        $row[] = '';
                    } else {
                        $row[] = $status;
                    }
                }

                $row[] = number_format($attendanceSummary[$student->id]['total_sakit'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_izin'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_alpha'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_points'], 1);
                $row[] = $attendanceSummary[$student->id]['warning'];

                $classroomData[] = $row;
            }

            $classroomData[] = [''];

            $data[] = $classroomData; // Add the classroom data to the main data array
        }

        return $data;
    }

    public function title()
    {
        return $this->typeClass->category;
    }

    private function getAbsencePoints()
    {
        return absence_point::all()->keyBy('hours_absent')->map(function ($item) {
            return $item->points;
        });
    }

    private function generateStudentAttendanceSummary($studentId, $startDate, $endDate, $absencePoints)
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

        if ($totalSickOccurrences >= 8) {
            $totalPoints += 0.5;
        }

        if ($totalPermissionOccurrences >= 8) {
            $totalPoints += 0.5;
        }

        return [
            'summary' => $summary,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'total_points' => $totalPoints,
            'warning' => $totalPoints > 2.9 ? 'panggilan orang tua' : ($totalPoints > 1.9 ? 'pemanggilan siswa' : ' ')
        ];
    }

    private function aggregateDailyAttendance($date, $studentId, $absencePoints)
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

            if ($status !== 'present' && isset($absencePoints[$attendance->hours])) {
                $points += $absencePoints[$attendance->hours];
            }

            $times[$status][] = $attendance->id;
        }

        $summaryString = '';
        foreach ($summary as $status => $hours) {
            if ($hours > 0 && $status !== 'present') {
                $statusShort = $this->convertStatusToShortForm($status);
                $summaryString .= "{$hours}{$statusShort}";
            }
        }

        // If the summary string is empty and there are hours present, set it to empty string
        if ($summary['present'] > 0 && $summaryString === '') {
            $summaryString = '';
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
}
