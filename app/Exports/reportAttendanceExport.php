<?php

namespace App\Exports;

use App\Models\Classroom;
use App\Models\Student;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportAttendanceExport implements WithMultipleSheets
{
    protected $classroomIds;
    protected $startDate;
    protected $endDate;

    public function __construct(array $classroomIds, $startDate, $endDate)
    {
        $this->classroomIds = $classroomIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];
        $classrooms = Classroom::whereIn('id', $this->classroomIds)->with('teacher')->get();

        foreach ($classrooms as $classroom) {
            $sheets[] = new SingleClassAttendanceSheet($classroom, $this->startDate, $this->endDate);
        }

        return $sheets;
    }
}

class SingleClassAttendanceSheet implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStyles, WithCustomStartCell
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
        return 'B6';
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
        $sheet->setCellValue('B2', 'Kelas:')
            ->setCellValue('C2', $this->classroom->name)
            ->setCellValue('B3', 'Wali Kelas:')
            ->setCellValue('C3', $teacherName)
            ->setCellValue('B4', 'Tanggal Rekap:')
            ->setCellValue('C4', date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate)));
    
        // Style header information
        $sheet->getStyle('B2:C4')->applyFromArray([
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
        $sheet->mergeCells('D5:' . $highestColumn . '5');  // Adjust column range as necessary
        $sheet->setCellValue('D5', 'Rekap');
    
        // Style "Rekap" header
        $sheet->getStyle('D5:' . $highestColumn . '5')->applyFromArray([
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
    
        // Merge cells for the headers in rows 6 and 7
        $sheet->mergeCells('B6:B7');  // NO column
        $sheet->mergeCells('C6:C7');  // Nama Siswa column
        $sheet->mergeCells('D6:D7');  // L/P column
    
        // Style headings
        $sheet->getStyle('B6:' . $highestColumn . '6')->applyFromArray([
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
        $sheet->getStyle('B6:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        // Auto size columns
        foreach (range(2, $highestColumnIndex) as $columnIndex) {  // Adjust the range to fit the number of columns
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }
    
        // Set specific row height for better appearance
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('2')->setRowHeight(20);
        $sheet->getRowDimension('3')->setRowHeight(20);
        $sheet->getRowDimension('4')->setRowHeight(20);
        $sheet->getRowDimension('5')->setRowHeight(20);
        $sheet->getRowDimension('6')->setRowHeight(20);  // Ensure header row height
        $sheet->getRowDimension('7')->setRowHeight(20);  // Ensure data row height
    
        return [
            6 => ['font' => ['bold' => true]],  // Ensure only header row is styled as bold
        ];
    }
    

    // public function styles(Worksheet $sheet)
    // {
    //     $teacherName = $this->classroom->teacher ? $this->classroom->teacher->name : 'N/A';

    //     // Add class name and teacher name above the table
    //     $sheet->setCellValue('A1', 'Class:');
    //     $sheet->setCellValue('B1', $this->classroom->name);
    //     $sheet->setCellValue('A2', 'Class Teacher:');
    //     $sheet->setCellValue('B2', $teacherName);

    //     // Add month and year
    //     $sheet->setCellValue('A3', 'Month:');
    //     $sheet->setCellValue('B3', date('F', strtotime($this->startDate)));
    //     $sheet->setCellValue('A4', 'Year:');
    //     $sheet->setCellValue('B4', date('Y', strtotime($this->startDate)));

    //     // Style headings
    //     $sheet->getStyle('A5:' . $sheet->getHighestColumn() . '5')->applyFromArray([
    //         'font' => [
    //             'bold' => true,
    //             'color' => ['argb' => '000000'],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => [
    //                 'argb' => '4F81BD', // Ganti dengan kode warna dari gambar
    //             ],
    //         ],
    //     ]);

    //     // Merge cells for header
    //     $sheet->mergeCells('A1:O1');
    //     $sheet->mergeCells('A2:O2');

    //     // Set column widths
    //     $sheet->getColumnDimension('A')->setWidth(5);
    //     $sheet->getColumnDimension('B')->setWidth(20);
    //     // Set the width for other columns as needed

    //     // Auto size columns
    //     foreach (range(1, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())) as $columnIndex) {
    //         $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
    //         $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    //     }

    //     return [
    //         5 => ['font' => ['bold' => true]],
    //     ];
    // }

    // Add an image/logo
    // $drawing = new Drawing();
    // $drawing->setName('Logo');
    // $drawing->setDescription('This is the logo');
    // $drawing->setPath(public_path('path/to/logo.png')); // Change to the path of your logo
    // $drawing->setHeight(50);
    // $drawing->setCoordinates('B1');
    // $drawing->setWorksheet($sheet);

    // public function styles(Worksheet $sheet)
    // {
    //     $teacherName = $this->classroom->teacher ? $this->classroom->teacher->name : 'N/A';

    //     // Add class name and teacher name above the table
    //     $sheet->setCellValue('A1', 'Class:');
    //     $sheet->setCellValue('B1', $this->classroom->name);
    //     $sheet->setCellValue('A2', 'Class Teacher:');
    //     $sheet->setCellValue('B2', $teacherName);

    //     // Add month and year
    //     $sheet->setCellValue('A3', 'Month:');
    //     $sheet->setCellValue('B3', date('F', strtotime($this->startDate)));
    //     $sheet->setCellValue('A4', 'Year:');
    //     $sheet->setCellValue('B4', date('Y', strtotime($this->startDate)));

    //     // Style headings
    //     $sheet->getStyle('A5:' . $sheet->getHighestColumn() . '5')->applyFromArray([
    //         'font' => [
    //             'bold' => true,
    //             'color' => ['argb' => '000000'], 
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //         // 'fill' => [
    //         //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //         //     'startColor' => [
    //         //         'argb' => '4F81BD',
    //         //     ],
    //         // ],
    //     ]);

    //     // Auto size columns
    //     foreach (range(1, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn())) as $columnIndex) {
    //         $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
    //         $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    //     }

    //     return [
    //         5 => ['font' => ['bold' => true]],
    //     ];
    // }
}
