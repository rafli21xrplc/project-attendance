<?php

namespace App\Exports;

use App\Models\attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SIAreportByTypeClassExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $classType;
    protected $classes;
    protected $months;
    protected $period;

    public function __construct($classType, $classes, $months, $period)
    {
        $this->classType = $classType;
        $this->classes = $classes;
        $this->months = $months;
        $this->period = $period;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;

        foreach ($this->classes as $className => $classData) {
            foreach ($classData['students'] as $student) {
                $row = [
                    'No' => $no++,
                    'Nama Siswa' => $student['name'],
                    'L/P' => $student['gender'],
                    'Nama Kelas' => $className,
                ];

                foreach ($this->months as $month) {
                    $row[] = $student['months'][$month]['sick'];
                    $row[] = $student['months'][$month]['permission'];
                    $row[] = $student['months'][$month]['alpha'];
                }

                $totalKehadiran = array_reduce($this->months, function ($carry, $month) use ($student) {
                    return $carry + $student['months'][$month]['sick'] + $student['months'][$month]['permission'] + $student['months'][$month]['alpha'];
                }, 0);

                $row[] = $totalKehadiran;
                $row[] = $student['total_tatib_points'];

                $rows[] = $row;
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = [
            ['No', 'Nama Siswa', 'L/P', 'Nama Kelas']
        ];

        $monthHeadings = [];
        foreach ($this->months as $month) {
            $monthHeadings[] = \Carbon\Carbon::parse($month)->translatedFormat('F Y');
            $monthHeadings[] = '';
            $monthHeadings[] = '';
        }

        $headings[] = array_merge(['No', 'Nama Siswa', 'L/P', 'Nama Kelas'], $monthHeadings, ['Jum. KTDHDRN', 'Poin Tatib']);

        return $headings;
    }

    public function title(): string
    {
        return $this->classType;
    }

    public function styles(Worksheet $sheet)
    {
        // Style for the header row
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        // Style for the body rows
        $bodyStyleArray = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF9F9F9'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        // Apply styles to header
        $sheet->getStyle('A1:Z2')->applyFromArray($headerStyleArray);

        // Apply styles to body
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A3:Z' . $highestRow)->applyFromArray($bodyStyleArray);

        // Set column widths
        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Merge cells for multi-row headers
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');

        $colIndex = 5; // Starting from column E
        foreach ($this->months as $month) {
            $sheet->mergeCellsByColumnAndRow($colIndex, 1, $colIndex + 2, 1);
            $sheet->setCellValueByColumnAndRow($colIndex, 1, \Carbon\Carbon::parse($month)->translatedFormat('F Y'));
            $colIndex += 3;
        }

        // Set headers for S, I, A under each month
        $colIndex = 5; // Starting from column E
        foreach ($this->months as $month) {
            $sheet->setCellValueByColumnAndRow($colIndex, 2, 'S');
            $sheet->setCellValueByColumnAndRow($colIndex + 1, 2, 'I');
            $sheet->setCellValueByColumnAndRow($colIndex + 2, 2, 'A');
            $colIndex += 3;
        }

        // Merge cells for attendance summary headers
        $summaryStartCol = $colIndex;
        $sheet->mergeCellsByColumnAndRow($summaryStartCol, 1, $summaryStartCol + 2, 1);
        $sheet->setCellValueByColumnAndRow($summaryStartCol, 1, 'Jum. KTDHDRN');
        $sheet->mergeCellsByColumnAndRow($summaryStartCol + 3, 1, $summaryStartCol + 3, 2);
        $sheet->setCellValueByColumnAndRow($summaryStartCol + 3, 1, 'Poin Tatib');

        // Set headers for S, I, A under Jum. KTDHDRN
        $sheet->setCellValueByColumnAndRow($summaryStartCol, 2, 'S');
        $sheet->setCellValueByColumnAndRow($summaryStartCol + 1, 2, 'I');
        $sheet->setCellValueByColumnAndRow($summaryStartCol + 2, 2, 'A');
    }
}
