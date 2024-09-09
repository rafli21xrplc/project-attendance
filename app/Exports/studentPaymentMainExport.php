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

class studentPaymentMainExport implements WithMultipleSheets
{
    protected $classrooms;
    protected $payment;
    protected $month;

    public function __construct($classrooms, $payment, $month)
    {
        $this->classrooms = $classrooms;
        $this->payment = $payment;
        $this->month = $month;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->classrooms as $classroom) {
            $sheets[] = new ClassPaymentSheet($classroom, $this->payment, $this->month);
        }

        return $sheets;
    }
}

class ClassPaymentSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $classroom;
    protected $payment;
    protected $month;

    public function __construct($classroom, $payment, $month)
    {
        $this->classroom = $classroom;
        $this->payment = $payment;
        $this->month = $month;
    }

    public function collection()
    {
        $data = collect();

        $rowNumber = 1;
        $totalTunggakan = 0;
        $totalDibayarkanBulanIni = 0;
        $totalDibayarkan = 0;

        $dynamicHeaders = ['NO', 'NISN', 'NAMA SISWA', 'WAKTU TUNGGAKAN', 'TUNGGAKAN', 'DIBAYARKAN BULAN ' . strtoupper($this->month), 'TOTAL DIBAYARKAN'];
        $monthHeaders = [];
        $maxMonths = 0;
        $studentPaymentsData = [];

        foreach ($this->classroom->students as $student) {
            $paymentData = [
                'no' => $rowNumber++,
                'nisn' => $student->nisn,
                'nama_siswa' => $student->name,
                'waktu_tunggakan' => 0,
                'tunggakan' => 0,
                'dibayarkan_bulan_ini' => 0, // Perhatikan ini masih dalam format numerik
                'total_dibayarkan' => 0, // Perhatikan ini masih dalam format numerik
            ];

            $monthlyPayments = [];
            $studentPayments = $student->studentPayments->where('payment_id', $this->payment->id);
            $totalDues = 0;

            foreach ($studentPayments as $studentPayment) {
                $totalDues += $studentPayment->payment->amount; // Total dues for this student

                $startMonth = Carbon::parse($studentPayment->payment->start_date)->startOfMonth();
                $endMonth = Carbon::parse($studentPayment->payment->end_date)->endOfMonth();

                $paymentData['waktu_tunggakan'] = Carbon::parse($startMonth)->format('F Y') . ' - ' . Carbon::parse($endMonth)->format('F Y');

                $months = CarbonPeriod::create($startMonth, '1 month', $endMonth);

                foreach ($months as $month) {
                    $formattedMonth = $month->translatedFormat('F Y');
                    $formattedMonthLower = strtolower($month->translatedFormat('F')); // august

                    $date = Carbon::createFromFormat('F', $formattedMonthLower);

                    $startDate = $date->startOfMonth()->toDateString();

                    $endDate = $date->endOfMonth()->toDateString();


                    $paidAmount = $studentPayment->paymentInstallments()
                        ->selectRaw('SUM(amount) as total')
                        ->whereRaw('LOWER(DATE_FORMAT(created_at, "%M")) = ?', [$formattedMonthLower])
                        ->value('total');

                    $paidAmountMonth = $studentPayment->paymentInstallments->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

                    $monthlyPayments[$formattedMonthLower] = $paidAmountMonth > 0 ? number_format($paidAmountMonth, 2) : '';

                    if ($formattedMonthLower == $this->month) {
                        $paymentData['dibayarkan_bulan_ini'] += $paidAmount;
                    }

                    $paymentData['total_dibayarkan'] += $paidAmount;

                    // Store Carbon instance for sorting
                    $monthHeaders[$formattedMonth] = $month;
                }
            }

            // Calculate tunggakan as total dues minus total dibayarkan
            $paymentData['tunggakan'] = $totalDues - $paymentData['total_dibayarkan'];

            // Tambahkan penjumlahan sebelum formatting
            $totalDibayarkanBulanIni += $paymentData['dibayarkan_bulan_ini'];
            $totalDibayarkan += $paymentData['total_dibayarkan'];
            $totalTunggakan += $paymentData['tunggakan'];

            // Format nilai setelah penjumlahan
            $paymentData['dibayarkan_bulan_ini'] = number_format($paymentData['dibayarkan_bulan_ini'], 2);
            $paymentData['total_dibayarkan'] = number_format($paymentData['total_dibayarkan'], 2);

            if ($paymentData['tunggakan'] <= 0) {
                $paymentData['tunggakan'] = "LUNAS";
            } else {
                $paymentData['tunggakan'] = number_format($paymentData['tunggakan'], 2);
            }

            // Merge the monthly payments into the main data array
            $paymentData = array_merge($paymentData, $monthlyPayments);

            $studentPaymentsData[] = $paymentData;
        }


        // Sort month headers by the actual date
        uksort($monthHeaders, function ($a, $b) use ($monthHeaders) {
            return $monthHeaders[$a]->timestamp - $monthHeaders[$b]->timestamp;
        });

        // Push headers
        $data->push(array_merge($dynamicHeaders, array_keys($monthHeaders)));

        // Push student data
        foreach ($studentPaymentsData as $paymentData) {
            $data->push($paymentData);
        }

        // Add totals row
        $totalRowData = [
            'no' => '',
            'nisn' => '',
            'nama_siswa' => '',
            'waktu_tunggakan' => 'TOTAL',
            'tunggakan' => number_format($totalTunggakan, 2),
            'dibayarkan_bulan_ini' => number_format($totalDibayarkanBulanIni, 2),
            'total_dibayarkan' => number_format($totalDibayarkan, 2),
        ];

        $data->push(array_merge($totalRowData, array_fill(0, count($monthHeaders), '')));

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $studentCount = count($this->classroom->students);
        $totalRow = $studentCount + 3;  // Adjust this based on where the total row lands

        $headerRange = 'A2:' . $sheet->getHighestColumn() . '2';  // Adjust based on the highest column dynamically
        $dataRange = 'A3:' . $sheet->getHighestColumn() . ($studentCount + 3);  // Covers all student data rows
        $totalRange = 'A' . $totalRow . ':' . $sheet->getHighestColumn() . $totalRow;  // Covers the total row

        // Merge title cell
        $sheet->mergeCells("A1:" . $sheet->getHighestColumn() . "1");
        $sheet->setCellValue('A1', 'REKAP TUNGGAKAN ' . $this->payment->name  . ' KELAS ' . $this->classroom->typeClass->category . ' ' . $this->classroom->name);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set fixed column widths
        $sheet->getColumnDimension('A')->setWidth(5);   // NO
        $sheet->getColumnDimension('B')->setWidth(15);  // NISN
        $sheet->getColumnDimension('C')->setWidth(35);  // NAMA SISWA
        $sheet->getColumnDimension('D')->setWidth(30);  // WAKTU TUNGGAKAN
        $sheet->getColumnDimension('E')->setWidth(20);  // TUNGGAKAN
        $sheet->getColumnDimension('F')->setWidth(35);  // DIBAYARKAN BULAN INI
        $sheet->getColumnDimension('G')->setWidth(20);  // TOTAL DIBAYARKAN

        // Set dynamic month column widths
        $highestColumn = $sheet->getHighestColumn();
        for ($col = 'G'; $col <= $highestColumn; $col++) {
            $sheet->getColumnDimension($col)->setWidth(20);  // Set month columns to width 20
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

        // Apply styles to total row
        $sheet->getStyle($totalRange)->applyFromArray([
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
    }

    public function headings(): array
    {
        return [
            'NO',
            'NISN',
            'NAMA SISWA',
            'WAKTU TUNGGAKAN',
            'TUNGGAKAN',
            'DIBAYARKAN BULAN ' . strtoupper($this->month),
            'DIBAYARKAN',
        ];
    }

    public function title(): string
    {
        return $this->classroom->typeClass->category . ' ' . $this->classroom->name;
    }
}
