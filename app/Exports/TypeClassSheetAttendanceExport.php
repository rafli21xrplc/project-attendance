<?php

namespace App\Exports;

use App\Models\student;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TypeClassSheetAttendanceExport  implements FromCollection, WithHeadings, WithTitle, WithStyles, WithCustomStartCell
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
        return collect();
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
        $currentRow = 1;

        foreach ($this->typeClass->classrooms as $classroom) {
            $students = $classroom->students;

            if ($students->isEmpty()) {
                continue;
            }

            $drawing = new Drawing();
            $drawing->setPath(public_path('assets/content/header_excel.png'));
            $drawing->setHeight(50);
            $drawing->setCoordinates('AC' . ($currentRow + 1));
            $drawing->setOffsetX(50);
            $drawing->setWorksheet($sheet);

            $sheet->setCellValue('B' . $currentRow, 'Kelas:')
                ->setCellValue('C' . $currentRow, $classroom->typeClass->category . ' ' . $classroom->name)
                ->mergeCells('C' . $currentRow . ':H' . $currentRow)
                ->setCellValue('B' . ($currentRow + 1), 'Wali Kelas:')
                ->setCellValue('C' . ($currentRow + 1), $classroom->teacher->name ?? 'N/A')
                ->mergeCells('C' . ($currentRow + 1) . ':H' . ($currentRow + 1))
                ->setCellValue('B' . ($currentRow + 2), 'Tanggal Rekap:')
                ->setCellValue('C' . ($currentRow + 2), date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate)))
                ->mergeCells('C' . ($currentRow + 2) . ':H' . ($currentRow + 2));

            $sheet->getStyle('B' . $currentRow . ':H' . ($currentRow + 2))->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $currentRow += 4;

            $dateRange = CarbonPeriod::create($this->startDate, 'last day of this month');
            $highestColumnIndex = 4 + ($dateRange->count() * 2);
            $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex);

            $sheet->mergeCells('E' . $currentRow . ':' . $highestColumn . $currentRow);
            $sheet->setCellValue('E' . $currentRow, 'Rekap');

            $sheet->getStyle('E' . $currentRow . ':' . $highestColumn . $currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFFFFF',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->mergeCells('B' . ($currentRow + 1) . ':B' . ($currentRow + 2));
            $sheet->mergeCells('C' . ($currentRow + 1) . ':C' . ($currentRow + 2));
            $sheet->mergeCells('D' . ($currentRow + 1) . ':D' . ($currentRow + 2));
            $sheet->mergeCells('E' . ($currentRow + 1) . ':E' . ($currentRow + 2));

            $headings = ['NO', 'NIS', 'NAMA SISWA', 'L/P'];
            foreach ($dateRange as $date) {
                $headings[] = $date->format('d');
                $headings[] = '';
            }

            $sheet->fromArray($headings, null, 'B' . ($currentRow + 1));
            $sheet->getStyle('B' . ($currentRow + 1) . ':' . $highestColumn . ($currentRow + 2))->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => '000000'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFFFFF',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            $currentRow += 3;

            $rowNumber = 1;
            foreach ($students as $student) {
                $row = [
                    $rowNumber++, $student->nis, $student->name, $student->gender
                ];
                foreach ($dateRange as $date) {
                    $dailySummary = app('App\Http\Controllers\admin\AttendanceReportController')->aggregateDailyAttendance($date->format('Y-m-d'), $student->id);
                    $row[] = $dailySummary;
                    $row[] = '';
                }
                $sheet->fromArray($row, null, 'B' . $currentRow++);
            }

            $sheet->getStyle('B' . ($currentRow - $students->count() - 3) . ':' . $highestColumn . ($currentRow - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->getColumnDimension('D')->setWidth(30);

            foreach (range(5, $highestColumnIndex) as $columnIndex) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
                $sheet->getColumnDimension($columnLetter)->setWidth(3);
            }

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
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            $currentRow += 10;
        }

        return [];
    }
}
