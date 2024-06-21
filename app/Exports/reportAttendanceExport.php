<?php

namespace App\Exports;

use App\Models\type_class;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportAttendanceExport implements WithMultipleSheets
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

    public function sheets(): array
    {
        $sheets = [];

        $typeClasses = type_class::whereIn('id', $this->typeClassIds)->with('classrooms.students', 'classrooms.teacher')->get();

        foreach ($typeClasses as $typeClass) {
            if ($typeClass->classrooms->count() > 0) {
                $sheets[] = new TypeClassSheetAttendanceExport($typeClass, $this->startDate, $this->endDate);
            }
        }

        if (empty($sheets)) {
            throw new \Exception("No sheets available to generate the report.");
        }

        return $sheets;
    }
}
