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

            $className = $attendance->student->classroom->typeClass->category . ' ' . $attendance->student->classroom->name;
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

            $classType = $attendance->student->classroom->typeClass->category; // Assuming typeClass relationship
            $className = $attendance->student->classroom->name;
            $teacherName = $attendance->student->classroom->teacher->name; // Assuming relationship is defined
            $studentId = $attendance->student->id;

            if (!isset($report[$classType])) {
                $report[$classType] = [
                    'students' => [],
                    'teacher_names' => []
                ];
            }

            if (!in_array($teacherName, $report[$classType]['teacher_names'])) {
                $report[$classType]['teacher_names'][] = $teacherName;
            }

            if (!isset($report[$classType]['students'][$studentId])) {
                $report[$classType]['students'][$studentId] = [
                    'class_name' => $className,
                    'name' => $attendance->student->name,
                    'gender' => $attendance->student->gender,
                    'months' => array_fill_keys($months, ['sick' => 0, 'permission' => 0, 'alpha' => 0, 'points' => 0]),
                    'total_tatib_points' => 0,
                    'warning' => ' - '
                ];
            }

            if (in_array($attendance->status, ['sick', 'permission', 'alpha'])) {
                $hours = $attendance->hours;
                $tatibPoints = $this->calculateTatibPoints($attendance->status, $hours);

                $report[$classType]['students'][$studentId]['months'][$month][$attendance->status]++;
                $report[$classType]['students'][$studentId]['months'][$month]['points'] += $tatibPoints;
            }
        }

        foreach ($report as &$classTypes) {
            foreach ($classTypes['students'] as &$student) {
                foreach ($student['months'] as $month => $statuses) {
                    $student['total_tatib_points'] += $statuses['points'];
                }

                // Determine if a warning is needed
                if ($student['total_tatib_points'] > 2.9) {
                    $student['warning'] = 'konfirmasi';
                } elseif ($student['total_tatib_points'] > 1.9) {
                    $student['warning'] = 'panggilan walimurid';
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
        return classRoom::with('typeClass')->with(['typeClass'])->get();
    }

    public function getSIALaporanBySearch($classroomIds)
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

            $className = $attendance->student->classroom->typeClass->category . ' ' . $attendance->student->classroom->name;
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
                    'warning' => ' - '
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

            if ($student['total_tatib_points'] > 2.9) {
                $student['warning'] = 'konfirmasi';
            } elseif ($student['total_tatib_points'] > 1.9) {
                $student['warning'] = 'panggilan orang tua';
            }
        }

        return [
            'period' => $currentPeriod->name,
            'report' => $report,
            'months' => $months,
        ];
    }
}
