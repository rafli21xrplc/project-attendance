<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class SIAreportByClassExport implements WithMultipleSheets
{
    // protected $report;
    // protected $months;
    // protected $className;
    // protected $teacherName;
    // protected $period;

    // public function __construct($report, $months, $className, $teacherName, $period)
    // {
    //     $this->report = $report;
    //     $this->months = $months;
    //     $this->className = $className;
    //     $this->teacherName = $teacherName;
    //     $this->period = $period;
    // }

    // public function collection()
    // {
    //     $data = [];

    //     foreach ($this->report as $student) {
    //         $row = [
    //             'Nama Siswa' => $student['name'],
    //             'L/P' => $student['gender'],
    //         ];

    //         foreach ($this->months as $month) {
    //             $row[$month . ' S'] = $student['months'][$month]['sick'];
    //             $row[$month . ' I'] = $student['months'][$month]['permission'];
    //             $row[$month . ' A'] = $student['months'][$month]['alpha'];
    //         }

    //         $row['Jum. KTDHDRN S'] = array_sum(array_column($student['months'], 'sick'));
    //         $row['Jum. KTDHDRN I'] = array_sum(array_column($student['months'], 'permission'));
    //         $row['Jum. KTDHDRN A'] = array_sum(array_column($student['months'], 'alpha'));
    //         $row['Poin Tatib'] = $student['total_tatib_points'];

    //         $data[] = $row;
    //     }

    //     return collect($data);
    // }

    // public function headings(): array
    // {
    //     $header1 = [
    //         'Nama Siswa', 'L/P'
    //     ];
    //     $header2 = [
    //         '', ''
    //     ];

    //     foreach ($this->months as $month) {
    //         $formattedMonth = \Carbon\Carbon::parse($month)->translatedFormat('F Y');
    //         $header1[] = $formattedMonth;
    //         $header1[] = '';
    //         $header1[] = '';
    //         $header2[] = 'S';
    //         $header2[] = 'I';
    //         $header2[] = 'A';
    //     }

    //     $header1[] = 'Jum. KTDHDRN';
    //     $header1[] = '';
    //     $header1[] = '';
    //     $header1[] = 'Poin Tatib';
    //     $header2[] = 'S';
    //     $header2[] = 'I';
    //     $header2[] = 'A';
    //     $header2[] = '';

    //     $titleInfo = [
    //         ['Nama Kelas', $this->className],
    //         ['Wali Kelas', $this->teacherName],
    //         ['Tanggal Rekapan', $this->period['start_date'] . ' - ' . $this->period['end_date']],
    //         ['Tahun Ajaran', $this->period['name']],
    //         [], // Empty row for spacing
    //     ];

    //     return array_merge($titleInfo, [$header1, $header2]);
    // }

    // public function title(): string
    // {
    //     return $this->className;
    // }

    // public function styles(Worksheet $sheet)
    // {
    //     // Merge and style title information cells
    //     $sheet->mergeCells('A1:H1');
    //     $sheet->mergeCells('A2:H2');
    //     $sheet->mergeCells('A3:H3');
    //     $sheet->mergeCells('A4:H4');

    //     $sheet->getStyle('A1:A4')->getFont()->setBold(true);
    //     $sheet->getStyle('A6:Z6')->getFont()->setBold(true);
    //     $sheet->getStyle('A7:Z7')->getFont()->setBold(true);

    //     $sheet->getStyle('A1:A4')->getAlignment()->setHorizontal('left');
    //     $sheet->getStyle('A6:Z6')->getAlignment()->setHorizontal('center');
    //     $sheet->getStyle('A7:Z7')->getAlignment()->setHorizontal('center');

    //     // Determine the range for the table
    //     $highestColumn = $sheet->getHighestColumn();
    //     $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    //     $highestRow = $sheet->getHighestRow();

    //     // Style "Rekap" header
    //     $sheet->mergeCells('D5:' . $highestColumn . '5');
    //     $sheet->setCellValue('D5', 'Rekap');

    //     $sheet->getStyle('D5:' . $highestColumn . '5')->applyFromArray([
    //         'font' => [
    //             'bold' => true,
    //             'color' => ['argb' => '000000'],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => [
    //                 'argb' => 'FFFFFF',
    //             ],
    //         ],
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //     ]);

    //     // Merge cells for the headers in rows 6 and 7
    //     $sheet->mergeCells('A6:A7');
    //     $sheet->mergeCells('B6:B7');
    //     $sheet->mergeCells('C6:C7');
    //     $sheet->mergeCells('D6:D7');

    //     // Style headings
    //     $sheet->getStyle('A6:' . $highestColumn . '6')->applyFromArray([
    //         'font' => [
    //             'bold' => true,
    //             'color' => ['argb' => '000000'],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => [
    //                 'argb' => 'FFFFFF',
    //             ],
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //     ]);

    //     // Calculate the highest row with data
    //     $highestRow = $sheet->getHighestDataRow();

    //     // Apply borders to the entire table
    //     $sheet->getStyle('A6:' . $highestColumn . $highestRow)->applyFromArray([
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //     ]);

    //     // Auto size columns
    //     foreach (range(1, $highestColumnIndex) as $columnIndex) {
    //         $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
    //         $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    //     }

    //     // Set specific row height for better appearance
    //     $sheet->getRowDimension('1')->setRowHeight(30);
    //     $sheet->getRowDimension('2')->setRowHeight(20);
    //     $sheet->getRowDimension('3')->setRowHeight(20);
    //     $sheet->getRowDimension('4')->setRowHeight(20);
    //     $sheet->getRowDimension('5')->setRowHeight(20);
    //     $sheet->getRowDimension('6')->setRowHeight(20);
    //     $sheet->getRowDimension('7')->setRowHeight(20);

    //     return [
    //         6 => ['font' => ['bold' => true]],
    //         7 => ['font' => ['bold' => true]],
    //     ];
    // }

    protected $report;
    protected $months;
    protected $period;

    public function __construct($report, $months, $period)
    {
        $this->report = $report;
        $this->months = $months;
        $this->period = $period;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->report as $className => $classData) {
            $teacherName = $classData['teacher_name'];
            $students = $classData['students'];
            $sheets[] = new SIAreportByTypeClassExport($students, $this->months, $className, $teacherName, $this->period);
        }

        return $sheets;
    }
}
