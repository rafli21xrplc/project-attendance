<?php

namespace App\Exports;

use App\Models\student_payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class studentPaymentMonthExport implements FromCollection, WithStyles
{
    protected $classrooms;
    protected $month;

    public function __construct($classrooms, $month)
    {
        $this->classrooms = $classrooms;
        $this->month = $month;
    }

    public function collection()
{
    $data = [];

    // Define the start and mid-month dates
    $startMonth = Carbon::parse($this->month)->startOfMonth();
    $midMonth = Carbon::parse($this->month)->startOfMonth()->addDays(15);

    $formattedStartMonth = $startMonth->translatedFormat('j-M-y');
    $formattedMidMonth = $midMonth->translatedFormat('j-M-y');

    // Define the headers
    $headers = [
        'KELAS',
        "$formattedStartMonth JUMLAH",
        "$formattedMidMonth JUMLAH",
        'KETERANGAN'
    ];

    // Add the headers as the first row
    $data[] = $headers;

    // Iterate over each class in classrooms
    foreach ($this->classrooms as $class) {
        $totalStartMonth = 0; // Reset class-level totals
        $totalMidMonth = 0;

        // Iterate over each student in the class
        foreach ($class->students as $student) {
            $totalStart = 0;
            $totalMid = 0;
            
            // Iterate through student payments
            foreach ($student->studentPayments as $studentPayment) {
                $totalDues = $studentPayment->payment->amount ?? 0;
            
                // Ensure installments are not null before applying the where filter
                $installments = $studentPayment->installments;
                if ($installments !== null) {
                    // Sum the installments paid by the start and mid-month dates
                    $paidStartMonth = $installments
                        ->where('payment_date', '<=', $startMonth->endOfDay())
                        ->sum('amount');
                    $paidMidMonth = $installments
                        ->where('payment_date', '<=', $midMonth->endOfDay())
                        ->sum('amount');
                } else {
                    $paidStartMonth = 0;
                    $paidMidMonth = 0;
                }
            
                // Calculate dues remaining at start and mid-month
                $totalStart += ($totalDues - $paidStartMonth);
                $totalMid += ($totalDues - $paidMidMonth);
            }

            // Accumulate totals for the class
            $totalStartMonth += $totalStart;
            $totalMidMonth += $totalMid;
        }

        // Determine the class-level progress status
        $status = ($totalMidMonth < $totalStartMonth) ? 'PROGRES' : 'TETAP';

        // Add the class-level summary row
        $data[] = [
            $class->typeClass->category . ' ' . $class->name,
            'Rp ' . number_format($totalStartMonth, 2),
            'Rp ' . number_format($totalMidMonth, 2),
            $status
        ];
    }

    // Return the collection of data
    return collect($data);
}


    public function styles(Worksheet $sheet)
    {
        // Get the last row and last column to determine the range of the table
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Define the range for the entire table
        $tableRange = 'A1:' . $lastColumn . $lastRow;

        // Apply styles to the header row
        $headerRange = 'A1:' . $lastColumn . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Apply borders to the entire table (including data rows)
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set fixed column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
    }
}
