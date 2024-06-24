<?php

namespace App\Exports;

use App\Models\absence_point;
use App\Models\attendance;
use App\Models\type_class;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $numDateColumns = iterator_count($dateRange); // Count the number of date columns
        $this->highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numDateColumns + 9); // Total columns including additional columns

        foreach ($this->typeClass->classrooms as $classroom) {
            $currentRow = $data->count() + 1; // Calculate the current row

            // Add class information
            $data->push(['Kelas:', $classroom->typeClass->category . ' ' . $classroom->name]);
            $data->push(['Wali Kelas:', $classroom->teacher->name ?? 'N/A']);
            $data->push(['Tanggal Rekap:', date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate))]);

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
            $this->tableStartRows[] = $currentRow + 4; // Mark the start of the table including header

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
                $row[] = $attendanceSummary[$student->id]['warning'];

                $data->push($row);
            }

            $this->tableEndRows[] = $data->count(); // Mark the end of the table

            // Add additional information below the table for the class
            $data->push(['']);
            $data->push(['Ket: *) Contoh penulisan sakit 1 hari = S', '', '']);
            $data->push(['      Contoh penulisan ijin 1 hari = I', 'Perhitungan jml Ketidakhadiran:', 'Malang, ' . date('d M Y')]);
            $data->push(['      Contoh penulisan alfa 1 hari = A', '1 jam = 0,1', '']);
            $data->push(['      Contoh penulisan sakit 2 jam = S2', '2 jam = 0,2', 'Waka Kesiswaan,']);
            $data->push(['      Contoh penulisan ijin 3 jam = I3', '3 jam = 0,3', '']);
            $data->push(['', '4 jam = 0,4', '']);
            $data->push(['', '5 jam = 0,5', 'Priyo Adi Nugroho, S.ST']);
            $data->push(['', '6 jam = 0,6', 'NIP. 19840517 201001 1 013']);
            $data->push(['', '7 jam = 0,7', '']);
            $data->push(['', '8 jam = 0,8', 'M. Amrul Jamrozi']);
            $data->push(['', '9 jam = 0,9', 'NIP.']);
            $data->push(['', '10 jam = 1', '']);

            // Add multiple blank rows for spacing between tables
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
        }

        // Apply specific styling for the additional information rows
        $highestRow = $sheet->getHighestRow();

        $additionalInfoRows = range($highestRow - 13, $highestRow);
        foreach ($additionalInfoRows as $row) {
            $sheet->getStyle("A{$row}:{$this->highestColumn}{$row}")->applyFromArray([
                'font' => [
                    'italic' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ]);
        }

        // Merge cells for the explanation and signatures
        foreach ($additionalInfoRows as $row) {
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->mergeCells("C{$row}:D{$row}");
            $sheet->mergeCells("E{$row}:{$this->highestColumn}{$row}");
        }

        // Adjust the width for the merged cells to be equal
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }

        $sheet->getColumnDimension('E')->setWidth(30);
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

        // Additional points if sick or permission occurrences reach 8
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
