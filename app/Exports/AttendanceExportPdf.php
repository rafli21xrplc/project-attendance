<?php

namespace App\Exports;

use App\Models\student;
use App\Models\type_class;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceExportPdf
{
    protected $typeClassIds;
    protected $startDate;
    protected $endDate;

    public function __construct(array $typeClassIds, $startDate, $endDate)
    {
        $this->typeClassIds = $typeClassIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getData()
    {
        $data = [];
        $typeClasses = type_class::whereIn('id', $this->typeClassIds)->get();

        foreach ($typeClasses as $typeClass) {
            $sheet = new TypeClassSheetAttendanceExportPdf($typeClass, $this->startDate, $this->endDate);
            $data[] = [
                'typeClass' => $typeClass,
                'data' => $sheet->collection(),
                'title' => $sheet->title()
            ];
        }

        return $data;
    }
}
