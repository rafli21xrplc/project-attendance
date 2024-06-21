<?php

namespace App\Exports;

use App\Models\attendance;
use App\Models\classRoom;
use App\Models\student;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClassAttendanceHomeroomExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell
{
    protected $classroomId;
    protected $startDate;
    protected $endDate;

    public function __construct($classroomId, $startDate, $endDate)
    {
        $this->classroomId = $classroomId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $classroom = Classroom::with('students', 'teacher')->findOrFail($this->classroomId);
        $students = $classroom->students;
        $data = collect();

        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($students as $index => $student) {
            $row = [
                $index + 1,
                $student->nis,
                $student->name,
                $student->gender
            ];

            foreach ($dateRange as $date) {
                $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $student->id);
                $row[] = $dailySummary['status'];
            }

            $data->push($row);
        }

        return $data;
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);
        $headings = ['NO', 'NIS', 'NAMA SISWA', 'L/P'];

        foreach ($dateRange as $date) {
            $headings[] = $date->format('d');
        }

        return $headings;
    }

    public function title(): string
    {
        return 'Class Attendance';
    }

    public function styles(Worksheet $sheet)
    {
        $classroom = Classroom::with('typeClass', 'teacher')->findOrFail($this->classroomId);
        $currentRow = 1;

        // Set the classroom information header
        $sheet->setCellValue('A' . $currentRow, 'Kelas:')
            ->setCellValue('B' . $currentRow, $classroom->typeClass->category . ' ' . $classroom->name)
            ->mergeCells('B' . $currentRow . ':F' . $currentRow)
            ->setCellValue('A' . ($currentRow + 1), 'Wali Kelas:')
            ->setCellValue('B' . ($currentRow + 1), $classroom->teacher->name ?? 'N/A')
            ->mergeCells('B' . ($currentRow + 1) . ':F' . ($currentRow + 1))
            ->setCellValue('A' . ($currentRow + 2), 'Tanggal Rekap:')
            ->setCellValue('B' . ($currentRow + 2), date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate)))
            ->mergeCells('B' . ($currentRow + 2) . ':F' . ($currentRow + 2));

        $sheet->getStyle('A' . $currentRow . ':F' . ($currentRow + 2))->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $currentRow += 4;

        // Set the table headings
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);
        $highestColumnIndex = 4 + $dateRange->count();
        $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex);

        $sheet->mergeCells('E' . $currentRow . ':' . $highestColumn . $currentRow);
        $sheet->setCellValue('E' . $currentRow, 'Rekap');

        $sheet->getStyle('E' . $currentRow . ':' . $highestColumn . $currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF',
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->mergeCells('A' . ($currentRow + 1) . ':A' . ($currentRow + 2));
        $sheet->mergeCells('B' . ($currentRow + 1) . ':B' . ($currentRow + 2));
        $sheet->mergeCells('C' . ($currentRow + 1) . ':C' . ($currentRow + 2));
        $sheet->mergeCells('D' . ($currentRow + 1) . ':D' . ($currentRow + 2));

        $headings = ['NO', 'NIS', 'NAMA SISWA', 'L/P'];
        foreach ($dateRange as $date) {
            $headings[] = $date->format('d');
        }

        $sheet->fromArray($headings, null, 'A' . ($currentRow + 1));
        $sheet->getStyle('A' . ($currentRow + 1) . ':' . $highestColumn . ($currentRow + 2))->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $currentRow += 3;

        $students = $classroom->students;
        $rowNumber = 1;

        foreach ($students as $student) {
            $row = [
                $rowNumber++, $student->nis, $student->name, $student->gender
            ];

            foreach ($dateRange as $date) {
                $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $student->id);
                $row[] = $dailySummary['status'];
            }

            $sheet->fromArray($row, null, 'A' . $currentRow++);
        }

        $sheet->getStyle('A' . ($currentRow - $students->count() - 3) . ':' . $highestColumn . ($currentRow - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Adjust column widths for better readability
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        foreach (range(5, $highestColumnIndex) as $columnIndex) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($columnLetter)->setWidth(3);
        }

        // Set the signature area
        $signatureColumn = 'AW';
        $signatureRow = $currentRow + 2;

        $sheet->setCellValue($signatureColumn . $signatureRow, 'Malang, ' . date('d M Y'));
        $sheet->setCellValue($signatureColumn . ($signatureRow + 2), 'Waka Kesiswaan,');
        $sheet->setCellValue($signatureColumn . ($signatureRow + 6), 'NIP.');

        $signatureColumn2 = 'BG';
        $sheet->setCellValue($signatureColumn2 . ($signatureRow + 2), 'Petugas,');
        $sheet->setCellValue($signatureColumn2 . ($signatureRow + 6), 'NIP.');

        $sheet->getStyle($signatureColumn . $signatureRow . ':' . $signatureColumn2 . ($signatureRow + 6))->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    private function aggregateDailyAttendance($date, $studentId)
    {
        $attendances = Attendance::whereDate('time', $date)
            ->where('student_id', $studentId)
            ->get();

        $summary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        foreach ($attendances as $attendance) {
            $status = $attendance->status;
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
            $summary[$status] += $attendance->hours;
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
