<?php

namespace App\Exports;

use App\Models\absence_point;
use App\Models\attendance;
use App\Models\type_class;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements WithMultipleSheets
{
    protected $typeClassIds;
    protected $startDate;
    protected $endDate;

    public function __construct(array $typeClassIds, $startDate, $endDate)
    {
        $this->typeClassIds = $typeClassIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        $typeClasses = type_class::whereIn('id', $this->typeClassIds)->get();

        foreach ($typeClasses as $typeClass) {
            $sheets[] = new TypeClassSheetAttendanceExport($typeClass, $this->startDate, $this->endDate);
        }

        return $sheets;
    }
}


class TypeClassSheetAttendanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell
{
    protected $typeClass;
    protected $startDate;
    protected $endDate;
    protected $tableStartRows = [];
    protected $tableEndRows = [];
    protected $highestColumn;
    protected $numDateColumns; // Add this property

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

        $startDate = Carbon::parse($this->startDate);
        $monthName = $startDate->format('F');

        foreach ($this->typeClass->classrooms as $classroom) {
            $currentRow = $data->count() + 1;

            // Add space for the logo
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);

            // Add class information
            $data->push(['Kelas:', $classroom->typeClass->category . ' ' . $classroom->name]);
            $data->push(['Wali Kelas:', $classroom->teacher->name ?? 'N/A']);
            $data->push(['Bulan:', $monthName]);
            $data->push(['Tahun Ajaran:', 'Semester Gasal - 24/25']);

            // Add a blank row for spacing
            $data->push(['']);

            // Add table header
            $header = ['NO', 'NISN', 'NAMA SISWA', 'L/P'];
            foreach ($dateRange as $date) {
                $header[] = $date->format('d');
            }
            $header[] = 'S';
            $header[] = 'I';
            $header[] = 'A';
            $header[] = 'POIN TATIB';
            $header[] = 'KET';

            $data->push($header);
            $this->tableStartRows[] = $currentRow + 9;

            // Add student data
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
                    $row[] = $attendanceSummary[$student->id]['summary'][$date->format('Y-m-d')] ?? '';
                }

                $row[] = number_format($attendanceSummary[$student->id]['total_sakit'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_izin'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_alpha'] * 0.1, 1);
                $row[] = number_format($attendanceSummary[$student->id]['total_points'], 1);

                // Calculate the Excel formula for KET column dynamically
                $pointColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->numDateColumns + 8);
                $currentDataRow = $data->count() + 1;
                $row[] = "=IF({$pointColumn}{$currentDataRow}<=0.9,\"\",IF({$pointColumn}{$currentDataRow}<=1.9,\"Konfirmasi\",IF({$pointColumn}{$currentDataRow}<=2.9,\"Konfirmasi ortu\",\"Panggilan ortu\")))";

                $data->push($row);
            }

            $this->tableEndRows[] = $data->count();



            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
            $data->push(['']);
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

    public function title(): string
    {
        return $this->typeClass->category;
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

            // Define the columns for Sakit, Izin, Alpha
            $sakitColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->numDateColumns + 5);
            $izinColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->numDateColumns + 6);
            $alphaColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($this->numDateColumns + 7);

            // Apply colors to the Sakit, Izin, Alpha columns
            $sheet->getStyle("{$sakitColumn}{$tableStartRow}:{$sakitColumn}{$tableEndRow}")
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF00'] // Yellow for Sakit
                    ]
                ]);

            $sheet->getStyle("{$izinColumn}{$tableStartRow}:{$izinColumn}{$tableEndRow}")
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => '00FF00'] // Green for Izin
                    ]
                ]);

            $sheet->getStyle("{$alphaColumn}{$tableStartRow}:{$alphaColumn}{$tableEndRow}")
                ->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF0000'] // Red for Alpha
                    ]
                ]);
        }

        foreach ($this->tableStartRows as $index => $tableStartRow) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath('public/assets/content/logo-excel.png'); // Path to your logo file
            $drawing->setHeight(80);
            $drawing->setCoordinates('A' . ($tableStartRow - 9)); // Adjust the position as needed
            $drawing->setWorksheet($sheet);
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
