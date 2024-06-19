<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SIAreportExport implements WithMultipleSheets
{
    // protected $report;
    // protected $months;
    // protected $period;

    // public function __construct($report, $months, $period)
    // {
    //     $this->report = $report;
    //     $this->months = $months;
    //     $this->period = $period;
    // }

    // public function sheets(): array
    // {
    //     $sheets = [];

    //     foreach ($this->report as $className => $classData) {
    //         $teacherName = $classData['teacher_name'];
    //         $students = $classData['students'];
    //         $sheets[] = new SIAreportByClassExport($students, $this->months, $className, $teacherName, $this->period);
    //     }

    //     return $sheets;
    // }

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
    
        foreach ($this->report as $className => $classData) {
            $teacherName = $classData['teacher_name'];
            $students = $classData['students'];
            $sheets[] = new SIAreportByClassExport($students, $this->months, $className, $teacherName, $this->period);
        }
    
        return $sheets;
    }
}
