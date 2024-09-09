<?php

namespace App\Exports;

use App\Models\attendanceLate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceLateExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return AttendanceLate::attendanceLateByDate($this->startDate, $this->endDate);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'HARI',
            'GURU',
            'KELAS',
            'PELAJARAN',
            'MULAI PELAJARAN',
            'AKHIR PELAJARAN',
            'WAKTU DETEKSI SYSTEM',
        ];
    }

    /**
     * @var AttendanceLate $item
     */
    public function map($item): array
    {
        static $rowNumber = 1;
        
        return [
            $rowNumber++,
            [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ][$item->day] ?? $item->day,
            $item->teacher_name,
            $item->type_class_category . ' ' . $item->class_name,
            $item->course_name,
            $item->start_time,
            $item->end_time,
            \Carbon\Carbon::parse($item->created_at)->locale('id')->formatLocalized('%d %B %Y %H:%M'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastDataRow = $this->collection()->count() + 1;
        $highestColumn = $sheet->getHighestColumn();
        
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getStyle("A1:{$highestColumn}{$lastDataRow}")->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setWidth(20);
        }

        $sheet->getColumnDimension('E')->setWidth(30);
    }
}