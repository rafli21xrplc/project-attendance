<?php

namespace App\Exports;

use App\Models\attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SIAreportByTypeClassExport implements FromCollection
{
    protected $report;
    protected $months;
    protected $className;
    protected $teacherName;
    protected $period;

    public function __construct($report, $months, $className, $teacherName, $period)
    {
        $this->report = $report;
        $this->months = $months;
        $this->className = $className;
        $this->teacherName = $teacherName;
        $this->period = $period;
    }

    public function collection()
    {
        $data = [];
        $no = 1;

        foreach ($this->report as $student) {
            $row = [
                'No' => $no++,
                'NIS' => $student['name'],
                'Nama Siswa' => $student['name'],
                'L/P' => $student['gender'],
            ];

            foreach ($this->months as $month) {
                $row[$month . ' S'] = $student['months'][$month]['sick'];
                $row[$month . ' I'] = $student['months'][$month]['permission'];
                $row[$month . ' A'] = $student['months'][$month]['alpha'];
            }

            $row['Jum. KTDHDRN S'] = array_sum(array_column($student['months'], 'sick'));
            $row['Jum. KTDHDRN I'] = array_sum(array_column($student['months'], 'permission'));
            $row['Jum. KTDHDRN A'] = array_sum(array_column($student['months'], 'alpha'));
            $row['Poin Tatib'] = $student['total_tatib_points'];

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $header1 = ['No', 'NIS', 'Nama Siswa', 'L/P'];
        $header2 = ['', '', '', ''];

        foreach ($this->months as $month) {
            $formattedMonth = \Carbon\Carbon::parse($month)->translatedFormat('F Y');
            $header1[] = $formattedMonth;
            $header1[] = '';
            $header1[] = '';
            $header2[] = 'S';
            $header2[] = 'I';
            $header2[] = 'A';
        }

        $header1[] = 'Jum. KTDHDRN';
        $header1[] = '';
        $header1[] = '';
        $header1[] = 'Poin Tatib';
        $header2[] = 'S';
        $header2[] = 'I';
        $header2[] = 'A';
        $header2[] = '';

        $titleInfo = [
            ['Nama Kelas', $this->className],
            ['Wali Kelas', $this->teacherName],
            ['Tanggal Rekapan', $this->period['start_date'] . ' - ' . $this->period['end_date']],
            ['Tahun Ajaran', $this->period['name']],
            [], // Empty row for spacing
        ];

        return array_merge($titleInfo, [$header1, $header2]);
    }

    public function title(): string
    {
        return $this->className;
    }

    public function styles(Worksheet $sheet)
    {
        // Insert the logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('School Logo');
        $drawing->setPath('assets/content/header_excel.png'); // Path to the logo file
        $drawing->setHeight(80); // Set the height of the logo
        $drawing->setCoordinates('N2'); // Position the logo at cell E1
        $drawing->setOffsetX(10); // Offset to center the logo if needed
        $drawing->setWorksheet($sheet);

        // Merge and style title information cells
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->mergeCells('A4:I4');

        $sheet->setCellValue('A1', 'Nama Kelas: ' . $this->className);
        $sheet->setCellValue('A2', 'Wali Kelas: ' . $this->teacherName);
        $sheet->setCellValue('A3', 'Tanggal Rekapan: ' . $this->period['start_date'] . ' - ' . $this->period['end_date']);
        $sheet->setCellValue('A4', 'Tahun Ajaran: ' . $this->period['name']);

        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A6:Z6')->getFont()->setBold(true);
        $sheet->getStyle('A7:Z7')->getFont()->setBold(true);

        $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('A6:Z6')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A7:Z7')->getAlignment()->setHorizontal('center');

        // Determine the range for the table
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $highestRow = $sheet->getHighestRow();

        // Style "Rekap" header
        $sheet->mergeCells('E5:' . $highestColumn . '5');
        $sheet->setCellValue('E5', 'Rekap');

        $sheet->getStyle('E5:' . $highestColumn . '5')->applyFromArray([
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

        // Merge cells for the headers in rows 6 and 7
        $sheet->mergeCells('A6:A7');  // No column
        $sheet->mergeCells('B6:B7');  // NIS column
        $sheet->mergeCells('C6:C7');  // Nama Siswa column
        $sheet->mergeCells('D6:D7');  // L/P column

        $columnIndex = 5;  // Starting from column E
        foreach ($this->months as $month) {
            $colStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $colEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 2);
            $sheet->mergeCells($colStart . '6:' . $colEnd . '6');
            $columnIndex += 3;
        }

        // Style headings
        $sheet->getStyle('A6:' . $highestColumn . '6')->applyFromArray([
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

        $sheet->getStyle('A7:' . $highestColumn . '7')->applyFromArray([
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

        // Ensure columns for S, I, and A have consistent width
        foreach (range('E', $highestColumn) as $column) {
            $sheet->getColumnDimension($column)->setWidth(15);
        }

        // Add columns for "Jum. KTDHDRN" and "Poin Tatib" with consistent width
        $colIndexForAttendance = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn) + 1;
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndexForAttendance))->setWidth(10);
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndexForAttendance + 1))->setWidth(10);
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndexForAttendance + 2))->setWidth(10);

        // Calculate the highest row with data
        $highestRow = $sheet->getHighestDataRow();

        // Apply borders to the entire table
        $sheet->getStyle('A6:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(20);
        $sheet->getRowDimension('6')->setRowHeight(20);
        $sheet->getRowDimension('7')->setRowHeight(20);

        // Add signature lines directly below the table
        $signatureRow = $highestRow + 2; // Adjusted to be right below the table
        $signatureColumn1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex - 10); // Adjust column for "Waka Kesiswaan"
        $signatureColumn2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($highestColumnIndex - 5); // Adjust column for "Petugas"

        $sheet->setCellValue($signatureColumn1 . $signatureRow, 'Malang, ' . date('d M Y'));
        $sheet->setCellValue($signatureColumn1 . ($signatureRow + 2), 'Waka Kesiswaan,');
        $sheet->setCellValue($signatureColumn1 . ($signatureRow + 6), 'NIP.');

        $sheet->setCellValue($signatureColumn2 . ($signatureRow + 2), 'Petugas,');
        $sheet->setCellValue($signatureColumn2 . ($signatureRow + 6), 'NIP.');

        $sheet->getStyle($signatureColumn1 . $signatureRow . ':' . $signatureColumn2 . ($signatureRow + 6))->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
        ];
    }
}
