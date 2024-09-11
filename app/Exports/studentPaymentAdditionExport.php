<?php

namespace App\Exports;

use App\Models\student_payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class studentPaymentAdditionExport implements WithMultipleSheets
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
        $studentPaymentsData = [];

        foreach ($this->classroom->students as $student) {
            $paymentData = [
                'no' => $rowNumber++,
                'nisn' => $student->nisn ?? 'Tidak Ada NISN',
                'nama_siswa' => $student->name ?? 'Tidak Ada Nama',
                'waktu_tunggakan' => 'N/A',
                'tunggakan' => 0,
                'dibayarkan_bulan_ini' => 0,
                'total_dibayarkan' => 0,
            ];

            $studentPayments = $student->studentPayments->where('payment_id', $this->payment->id);

            if ($studentPayments->isEmpty()) {
                $studentPayments = student_payment::where('student_id', $student->id)->where('payment_id', $this->payment->id)->get();
            }

            if ($studentPayments->isEmpty()) {
                $studentPaymentsData[] = $paymentData;
                continue;
            }

            $totalDues = 0;

            foreach ($studentPayments as $studentPayment) {
                $paymentDataDb = DB::table('student_payment')
                    ->join('payment', 'student_payment.payment_id', '=', 'payment.id')
                    ->where('student_payment.id', $studentPayment->id)
                    ->select('payment.amount', 'payment.start_date', 'payment.end_date')
                    ->first();

                if ($paymentDataDb) {
                    $totalDues += $paymentDataDb->amount ?? 0;

                    $startMonth = Carbon::parse($paymentDataDb->start_date)->startOfMonth();
                    $endMonth = Carbon::parse($paymentDataDb->end_date)->endOfMonth();
                    $paymentPeriod = $startMonth->format('F Y') . ' - ' . $endMonth->format('F Y');
                    $paymentData['waktu_tunggakan'] = $paymentPeriod ?? 'N/A';

                    $months = CarbonPeriod::create($startMonth, '1 month', $endMonth);
                    foreach ($months as $month) {
                        $formattedMonthLower = strtolower($month->translatedFormat('F'));

                        $paidAmount = $studentPayment->paymentInstallments()
                            ->selectRaw('SUM(amount) as total')
                            ->whereRaw('LOWER(DATE_FORMAT(created_at, "%M")) = ?', [$formattedMonthLower])
                            ->value('total') ?? 0;

                        if ($formattedMonthLower == strtolower($this->month)) {
                            $paymentData['dibayarkan_bulan_ini'] += $paidAmount ?? 0;
                        }

                        $paymentData['total_dibayarkan'] += $paidAmount ?? 0;
                    }
                } else {
                    $paymentData['waktu_tunggakan'] = 'N/A';
                }
            }

            $paymentData['tunggakan'] = $totalDues - $paymentData['total_dibayarkan'];

            if ($paymentData['tunggakan'] <= 0) {
                $paymentData['tunggakan'] = "LUNAS";
            } else {
                $paymentData['tunggakan'] = $paymentData['tunggakan'];
            }

            // Accumulate the raw numeric values
            $totalTunggakan += ($paymentData['tunggakan'] !== "LUNAS") ? floatval($paymentData['tunggakan']) : 0;
            $totalDibayarkanBulanIni += $paymentData['dibayarkan_bulan_ini'];
            $totalDibayarkan += $paymentData['total_dibayarkan'];

            // Format numbers for display
            $paymentData['dibayarkan_bulan_ini'] = number_format($paymentData['dibayarkan_bulan_ini'], 2);
            $paymentData['total_dibayarkan'] = number_format($paymentData['total_dibayarkan'], 2);
            $paymentData['tunggakan'] = ($paymentData['tunggakan'] !== "LUNAS") ? number_format($paymentData['tunggakan'], 2) : "LUNAS";

            $studentPaymentsData[] = $paymentData;
        }

        $data->push($dynamicHeaders);

        foreach ($studentPaymentsData as $paymentData) {
            $data->push($paymentData);
        }

        // Total row with formatted values
        $totalRowData = [
            'no' => '',
            'nisn' => '',
            'nama_siswa' => '',
            'waktu_tunggakan' => 'TOTAL',
            'tunggakan' => number_format($totalTunggakan, 2),
            'dibayarkan_bulan_ini' => number_format($totalDibayarkanBulanIni, 2),
            'total_dibayarkan' => number_format($totalDibayarkan, 2),
        ];
        $data->push($totalRowData);

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $studentCount = count($this->classroom->students);
        $totalRow = $studentCount + 3;

        $headerRange = 'A2:' . $sheet->getHighestColumn() . '2';
        $dataRange = 'A3:' . $sheet->getHighestColumn() . ($studentCount + 3);
        $totalRange = 'A' . $totalRow . ':' . $sheet->getHighestColumn() . $totalRow;

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
