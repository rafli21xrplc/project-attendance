<?php

namespace App\Exports;

use App\Models\student;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class singleClassAttendanceExport implements FromCollection
{
    protected $classroom;
    protected $startDate;
    protected $endDate;

    public function __construct($classroom, $startDate, $endDate)
    {
        $this->classroom = $classroom;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Student::where('classroom_id', $this->classroom->id)->get();
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        $headings = ['NO', 'NAMA SISWA', 'GENDER'];
        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($dateRange as $date) {
            $headings[] = $date->format('d');  // Only day number
        }

        return $headings;
    }

    public function title(): string
    {
        return $this->classroom->name;
    }

    public function map($student): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $dateRange = CarbonPeriod::create($this->startDate, $this->endDate);
        $row = [$rowNumber, $student->name, $student->gender];

        foreach ($dateRange as $date) {
            $dailySummary = app('App\Http\Controllers\admin\AttendanceReportController')->aggregateDailyAttendance($date->format('Y-m-d'), $student->id);
            $row[] = $dailySummary;
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $teacherName = $this->classroom->teacher ? $this->classroom->teacher->name : 'N/A';

        // Set class name and teacher name above the table
        $sheet->setCellValue('A1', 'Kelas:')
            ->setCellValue('B1', $this->classroom->name)
            ->setCellValue('A2', 'Wali Kelas:')
            ->setCellValue('B2', $teacherName)
            ->setCellValue('A3', 'Tanggal Rekap:')
            ->setCellValue('B3', date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate)));

        // Style header information
        $sheet->getStyle('A1:B3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Determine the range for the table
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $highestRow = $sheet->getHighestRow();

        // Merge cells for "Rekap" header
        $sheet->mergeCells('C4:' . $highestColumn . '4');
        $sheet->setCellValue('C4', 'Rekap');

        // Style "Rekap" header
        $sheet->getStyle('C4:' . $highestColumn . '4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],  // Set the font color to black
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF',  // White background for "Rekap" header
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Merge cells for the headers in rows 5 and 6
        $sheet->mergeCells('A5:A6');  // NO column
        $sheet->mergeCells('B5:B6');  // Nama Siswa column
        $sheet->mergeCells('C5:C6');  // L/P column

        // Style headings
        $sheet->getStyle('A5:' . $highestColumn . '6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],  // Set the font color to black
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFF',  // White background for headings
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Calculate the highest row with data
        $highestRow = $sheet->getHighestDataRow();

        // Apply borders to the entire table
        $sheet->getStyle('A5:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto size columns
        foreach (range(1, $highestColumnIndex) as $columnIndex) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        // Set specific row height for better appearance
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(20);
        $sheet->getRowDimension('6')->setRowHeight(20); 
        $sheet->getRowDimension('7')->setRowHeight(20); 

        return [
            5 => ['font' => ['bold' => true]],  // Ensure only header row is styled as bold
        ];
    }
}
