<?php

namespace App\Exports;

use App\Models\absence_point;
use App\Models\attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class attendanceAllStudentExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell
{
    protected $typeClass;
    protected $startDate;
    protected $endDate;
    protected $tableStartRows = [];
    protected $tableEndRows = [];
    protected $highestColumn;
    protected $numDateColumns;

    public function __construct($typeClass, $startDate, $endDate)
    {
        $this->typeClass = $typeClass;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = collect();
        $absencePoints = $this->getAbsencePoints();
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);
        $this->numDateColumns = iterator_count($dateRange);
        $this->highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->numDateColumns + 9);

        // Add the header row
        $header = ['NO', 'NISN', 'NAMA SISWA', 'L/P', 'KELAS'];
        foreach ($dateRange as $date) {
            $header[] = $date->format('d');
        }

        $data->push($header);

        $rowNumber = 1;

        foreach ($this->typeClass as $typeClass) {
            foreach ($typeClass->classrooms as $classroom) {
                // Add student data
                $attendanceSummary = [];
                foreach ($classroom->students as $student) {
                    $attendanceSummary[$student->id] = $this->generateStudentAttendanceSummary($student->id, $this->startDate, $this->endDate, $absencePoints);
                }

                foreach ($classroom->students as $student) {
                    $row = [
                        $rowNumber++,
                        $student->student_id,
                        $student->name,
                        $student->gender,
                        $classroom->typeClass->category . ' ' . $classroom->name // Class information
                    ];

                    foreach ($dateRange as $date) {
                        $row[] = $attendanceSummary[$student->id]['summary'][$date->format('Y-m-d')] ?? '';
                    }
                    $data->push($row);
                }
            }
        }

        return $data;
    }


    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:Z1000')->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
        ]);

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A')->getFont()->setBold(true);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        // Apply specific columns' widths
        $sheet->getColumnDimension('D')->setWidth(30);

        // Apply specific styling for headers and table
        foreach ($this->tableStartRows as $index => $tableStartRow) {
            $tableEndRow = $this->tableEndRows[$index];
            $sheet->getStyle("A{$tableStartRow}:{$this->highestColumn}{$tableEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);
        }
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

        foreach ($dateRange as $date) {
            $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $studentId, $absencePoints);
            $summary[$date->format('Y-m-d')] = $dailySummary['status'];
            $totalHadir += $dailySummary['summary']['present'];
            $totalIzin += $dailySummary['summary']['permission'];
            $totalSakit += $dailySummary['summary']['sick'];
            $totalAlpha += $dailySummary['summary']['alpha'];
            $totalPoints += $dailySummary['points'];
        }


        if ($totalSakit >= 8) {
            $totalPoints += 0.5;
        }

        if ($totalIzin >= 8) {
            $totalPoints += 0.5;
        }


        $totalPoints += ($totalAlpha * 0.1);

        return [
            'summary' => $summary,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'total_points' => $totalAlpha * 7,
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
        }

        $summaryString = '';
        if ($summary['present'] > 0 && $summary['permission'] == 0 && $summary['sick'] == 0 && $summary['alpha'] == 0) {
            $summaryString = '';
        } else {
            foreach ($summary as $status => $hours) {
                if ($hours > 0 && $status !== 'present') {
                    $statusShort = $this->convertStatusToShortForm($status);
                    $summaryString .= "{$hours}{$statusShort}";
                }
            }
        }

        return [
            'status' => $summaryString,
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
