<?php


namespace App\Traits;

use App\Models\absence_point;
use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use Carbon\Carbon;

trait SIATrait
{
    public function getSIALaporan(array $classroomIds)
    {
        if (empty($classroomIds)) {
            $classroomIds = [classRoom::first()->id];
        }

        $currentPeriod = kbm_period::getCurrentPeriod();
        if (!$currentPeriod) {
            return response()->json(['message' => 'No active KBM period found.'], 404);
        }

        $startDate = Carbon::parse($currentPeriod->start_date);
        $endDate = Carbon::parse($currentPeriod->end_date);
        $months = [];

        while ($startDate->lte($endDate)) {
            $months[] = $startDate->format('Y-m');
            $startDate->addMonth();
        }

        $report = [];

        $attendances = Attendance::where('kbm_period_id', $currentPeriod->id)
            ->whereHas('student.classroom', function ($query) use ($classroomIds) {
                $query->whereIn('id', $classroomIds);
            })
            ->orderBy('student_id')
            ->orderBy('time')
            ->get();

        foreach ($attendances as $attendance) {
            $month = Carbon::parse($attendance->time)->format('Y-m');

            $className = $attendance->student->classroom->name;
            $studentId = $attendance->student->id;

            if (!isset($report[$className])) {
                $report[$className] = [];
            }

            if (!isset($report[$className][$studentId])) {
                $report[$className][$studentId] = [
                    'name' => $attendance->student->name,
                    'gender' => $attendance->student->gender,
                    'months' => array_fill_keys($months, ['sick' => 0, 'permission' => 0, 'alpha' => 0, 'points' => 0]),
                    'total_tatib_points' => 0,
                ];
            }

            if (in_array($attendance->status, ['sick', 'permission', 'alpha'])) {
                $hours = $attendance->hours;
                $tatibPoints = $this->calculateTatibPoints($attendance->status, $hours);

                $report[$className][$studentId]['months'][$month][$attendance->status]++;
                $report[$className][$studentId]['months'][$month]['points'] += $tatibPoints;
            }
        }

        foreach ($report as $className => &$students) {
            foreach ($students as &$student) {
                foreach ($student['months'] as $month => $statuses) {
                    $student['total_tatib_points'] += $statuses['points'];
                }
            }
        }

        return [
            'period' => $currentPeriod->name,
            'report' => $report,
            'months' => $months,
        ];
    }



    public function getSIALaporanExcel(array $classroomIds)
    {
        if (empty($classroomIds)) {
            $classroomIds = [classRoom::first()->id];
        }

        $currentPeriod = kbm_period::getCurrentPeriod();
        if (!$currentPeriod) {
            return response()->json(['message' => 'No active KBM period found.'], 404);
        }

        $startDate = Carbon::parse($currentPeriod->start_date);
        $endDate = Carbon::parse($currentPeriod->end_date);
        $months = [];

        while ($startDate->lte($endDate)) {
            $months[] = $startDate->format('Y-m');
            $startDate->addMonth();
        }

        $report = [];

        $attendances = Attendance::where('kbm_period_id', $currentPeriod->id)
            ->whereHas('student.classroom', function ($query) use ($classroomIds) {
                $query->whereIn('id', $classroomIds);
            })
            ->orderBy('student_id')
            ->orderBy('time')
            ->get();

        foreach ($attendances as $attendance) {
            $month = Carbon::parse($attendance->time)->format('Y-m');

            $className = $attendance->student->classroom->name;
            $teacherName = $attendance->student->classroom->teacher->name; // Assuming relationship is defined
            $studentId = $attendance->student->id;

            if (!isset($report[$className])) {
                $report[$className] = [
                    'teacher_name' => $teacherName,
                    'students' => []
                ];
            }

            if (!isset($report[$className]['students'][$studentId])) {
                $report[$className]['students'][$studentId] = [
                    'name' => $attendance->student->name,
                    'gender' => $attendance->student->gender,
                    'months' => array_fill_keys($months, ['sick' => 0, 'permission' => 0, 'alpha' => 0, 'points' => 0]),
                    'total_tatib_points' => 0,
                ];
            }

            if (in_array($attendance->status, ['sick', 'permission', 'alpha'])) {
                $hours = $attendance->hours;
                $tatibPoints = $this->calculateTatibPoints($attendance->status, $hours);

                $report[$className]['students'][$studentId]['months'][$month][$attendance->status]++;
                $report[$className]['students'][$studentId]['months'][$month]['points'] += $tatibPoints;
            }
        }

        foreach ($report as $className => &$classData) {
            foreach ($classData['students'] as &$student) {
                foreach ($student['months'] as $month => $statuses) {
                    $student['total_tatib_points'] += $statuses['points'];
                }
            }
        }

        return [
            'period' => [
                'name' => $currentPeriod->name,
                'start_date' => $currentPeriod->start_date,
                'end_date' => $currentPeriod->end_date,
            ],
            'report' => $report,
            'months' => $months,
        ];

    }

    private function calculateTatibPoints($status, $hours)
    {
        $pointsPerHour = [
            'sick' => 0,
            'permission' => 0,
            'alpha' => 0.1,
        ];

        if (array_key_exists($status, $pointsPerHour)) {
            return $pointsPerHour[$status] * $hours;
        }

        return 0;
    }


    public function getClassroom()
    {
        return classRoom::with('typeClass')->get();
    }

    public function getSIALaporanBySearch($classroomId)
    {
        $currentPeriod = kbm_period::getCurrentPeriod();
        if (!$currentPeriod) {
            return response()->json(['message' => 'No active KBM period found.'], 404);
        }

        $startDate = Carbon::parse($currentPeriod->start_date);
        $endDate = Carbon::parse($currentPeriod->end_date);
        $months = [];

        while ($startDate->lte($endDate)) {
            $months[$startDate->format('Y-m')] = ['sick' => 0, 'permission' => 0, 'alpha' => 0];
            $startDate->addMonth();
        }

        $attendancesQuery = Attendance::where('kbm_period_id', $currentPeriod->id)
            ->orderBy('student_id')
            ->orderBy('time');

        if ($classroomId) {
            $attendancesQuery->whereHas('student.classroom', function ($query) use ($classroomId) {
                $query->where('id', $classroomId);
            });
        }

        $attendances = $attendancesQuery->get();

        $report = [];

        foreach ($attendances as $attendance) {
            $month = Carbon::parse($attendance->time)->format('Y-m');
            $day = Carbon::parse($attendance->time)->format('d');

            if (!isset($report[$attendance->student->classroom->name])) {
                $report[$attendance->student->classroom->name] = [];
            }

            if (!isset($report[$attendance->student->classroom->name][$attendance->student->id])) {
                $report[$attendance->student->classroom->name][$attendance->student->id] = [
                    'name' => $attendance->student->name,
                    'gender' => $attendance->student->gender,
                    'days' => [],
                    'total_tatib_points' => 0,
                ];
            }

            if (!isset($report[$attendance->student->classroom->name][$attendance->student->id]['days'][$day])) {
                $report[$attendance->student->classroom->name][$attendance->student->id]['days'][$day] = [
                    'sick' => 0,
                    'permission' => 0,
                    'alpha' => 0,
                    'points' => 0,
                ];
            }

            $hours = $attendance->hours; // Assuming 'hours' is the field storing number of hours the status applies to
            $tatibPoints = $this->calculateTatibPoints($attendance->status, $hours);

            if (in_array($attendance->status, ['sick', 'permission', 'alpha'])) {
                $report[$attendance->student->classroom->name][$attendance->student->id]['days'][$day][$attendance->status]++;
                $report[$attendance->student->classroom->name][$attendance->student->id]['days'][$day]['points'] += $tatibPoints;
            }
        }

        foreach ($report as $className => &$students) {
            foreach ($students as &$student) {
                foreach ($student['days'] as $day => $statuses) {
                    $student['total_tatib_points'] += $statuses['points'];
                }
            }
        }

        return [
            'period' => $currentPeriod->name,
            'report' => $report,
        ];
    }
}
