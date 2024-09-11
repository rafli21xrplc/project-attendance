<?php

namespace App\Exports;

use App\Models\PaymentInstallment;
use App\Models\student_payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class paymentInstallmentExport implements WithMultipleSheets
{
    protected $classrooms;
    protected $payment;

    public function __construct($classrooms, $payment)
    {
        $this->classrooms = $classrooms;
        $this->payment = $payment;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->classrooms as $classroom) {
            $sheets[] = new ClassPaymentSheet($classroom, $this->payment);
        }

        return $sheets;
    }
}
class ClassPaymentSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $classroom;
    protected $payment;

    public function __construct($classroom, $payment)
    {
        $this->classroom = $classroom;
        $this->payment = $payment;
    }

    public function collection()
    {
        $data = collect();
        $rowNumber = 1;

        // Collect student data
        foreach ($this->classroom->students as $student) {
            $paymentData = [
                'no' => $rowNumber++,
                'nisn' => $student->nisn,
                'nama_siswa' => $student->name,
            ];

            $studentPayments = $student->studentPayments->where('payment_id', $this->payment->id);

            if ($studentPayments->isEmpty()) {
                // If no student payments are found, retrieve them from the database
                $studentPayments = student_payment::where('student_id', $student->id)
                    ->where('payment_id', $this->payment->id)
                    ->get();
            }

            // Now, flatMap to get the installments from the student payments
            $installments = $studentPayments->flatMap(function ($studentPayment) {
                return $studentPayment->paymentInstallments;
            });


            // Add installment amounts to the paymentData array
            $installmentCounter = 1;
            foreach ($installments as $installment) {
                $paymentData['installment_' . $installmentCounter++] = 'Rp. ' . number_format($installment->amount, 2) . ' - ' . Carbon::parse($installment->created_at)->formatLocalized('%d %B %Y');
            }

            $data->push($paymentData);
        }

        return $data;
    }

    public function headings(): array
    {
        // Calculate the maximum number of installments across all students
        $maxInstallments = $this->classroom->students->flatMap(function ($student) {
            return $student->studentPayments
                ->where('payment_id', $this->payment->id)
                ->flatMap(function ($studentPayment) {
                    return $studentPayment->paymentInstallments;
                });
        })->count();

        // Base headers
        $headers = [
            'NO',
            'NISN',
            'NAMA SISWA',
        ];

        for ($i = 1; $i <= $maxInstallments; $i++) {
            $headers[] = (string)$i;
        }

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $studentCount = $this->collection()->count();

        $headerRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $dataRange = 'A2:' . $sheet->getHighestColumn() . ($studentCount + 1);

        // Set fixed column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // NO
        $sheet->getColumnDimension('B')->setWidth(15);  // NISN
        $sheet->getColumnDimension('C')->setWidth(40);  // NAMA SISWA

        // Set dynamic column widths for installments
        for ($col = 'D'; $col <= $sheet->getHighestColumn(); $col++) {
            $sheet->getColumnDimension($col)->setWidth(30);  // Set installment columns to width 20
        }

        // Apply styles to header row
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

        // Apply styles to data rows
        $sheet->getStyle($dataRange)->applyFromArray([
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
    }

    public function title(): string
    {
        return $this->classroom->typeClass->category . ' ' . $this->classroom->name;
    }
}
