<?php


namespace App\Traits;

use App\Models\absence_point;
use App\Models\attendance;
use Carbon\CarbonPeriod;

trait ReportAttendanceHomeTeacherPDFTrait
{

        public function generateAttendanceSummary($classroom, $startDate, $endDate)
        {
                $dateRange = CarbonPeriod::create($startDate, $endDate);
                $attendanceSummary = [];

                foreach ($classroom->students as $student) {
                        $attendanceSummary[$student->id] = $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate, $this->getAbsencePoints());
                }

                return $attendanceSummary;
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
                        'warning' => $totalPoints > 2.9 ? 'Parent Call' : ($totalPoints > 1.9 ? 'Student Call' : 'None')
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
}
