<?php

namespace App\Exports;

use App\Models\type_class;
use App\Models\classRoom;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SIAreportExport implements WithMultipleSheets
{
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
        $classTypes = [];

        // Group classes by their types using type_class_id from classroom
        foreach ($this->report as $className => $classData) {
            // Fetch type_class_id from classroom model based on class name
            $classroom = classRoom::where('name', $className)->first();
            if ($classroom) {
                $classType = $classroom->typeClass->category; // Assuming category field is the class type (X, XI, XII, etc.)

                if (!isset($classTypes[$classType])) {
                    $classTypes[$classType] = [
                        'type' => $classType,
                        'classes' => []
                    ];
                }

                $classTypes[$classType]['classes'][$className] = $classData;
            }
        }

        // Create a sheet for each class type
        foreach ($classTypes as $classType) {
            $sheets[] = new SIAreportByTypeClassExport($classType['classes'], $this->months, $classType['type'], $this->period);
        }

        return $sheets;
    }
}
