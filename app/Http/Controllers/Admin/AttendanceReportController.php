<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Exports\reportAttendanceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendancReporteRequest;
use App\Models\attendance;
use App\Models\student;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{
    private AttendanceRekapInterface $attendanceRekapInterface;

    public function __construct(AttendanceRekapInterface $attendanceRekapInterface)
    {
        $this->attendanceRekapInterface = $attendanceRekapInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = $this->attendanceRekapInterface->getClassroom();
        return view('admin.attendance_report', [
            'report' => null,
            'classroom' => null,
            'startDate' => null,
            'endDate' => null,
            'classrooms' => $classrooms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(SearchAttendancReporteRequest $request)
    {
        $classrooms = $this->attendanceRekapInterface->getClassroom();
        $report = $this->attendanceRekapInterface->getAttendanceStudent($request->validated());
        return view('admin.attendance_report', [
            'report' => $report['report'],
            'classroom' => $report['classroom'],
            'startDate' => $report['startDate'],
            'endDate' => $report['endDate'],
            'classrooms' => $classrooms
        ]);
    }


    public function export(Request $request)
    {
        // $classroomIds = $request->input('classroom_ids');
        // $startDate = $request->input('start_date');
        // $endDate = $request->input('end_date');

        $classroomIds = ["5ea177d1-2aa7-330e-8500-7a354e96c82a", "91c1ba44-ca9b-34d7-92e5-6bdf21b4def0"];
        $startDate = "2024-5-10";
        $endDate = "2024-6-10";

        return Excel::download(new reportAttendanceExport($classroomIds, $startDate, $endDate), 'class_attendance_report.xlsx');
    }

    public function aggregateDailyAttendance($date, $studentId)
    {
        $attendances = attendance::whereDate('time', $date)
            ->where('student_id', $studentId)
            ->get();

        $summary = [
            'present' => 0,
            'permission' => 0,
            'sick' => 0,
            'alpha' => 0,
        ];

        foreach ($attendances as $attendance) {
            $summary[$attendance->status] += $attendance->hours;
        }

        $summaryString = '';
        if ($summary['permission'] > 0) {
            $summaryString .= "{$summary['permission']}i";
        }
        if ($summary['present'] > 0) {
            $summaryString .= "{$summary['present']}H";
        }
        if ($summary['sick'] > 0) {
            $summaryString .= "{$summary['sick']}S";
        }
        if ($summary['alpha'] > 0) {
            $summaryString .= "{$summary['alpha']}A";
        }

        return $summaryString;
    }


    public function generateClassMonthlyReport($classroomId, $startDate, $endDate)
    {

        $students = student::whereIn('classroom_id', ["4df624b6-08e8-3753-a1e1-4b7cefaa15d4", "bd1bab50-17e0-364f-bd58-f1749bb7288f"])->get();
        $report = [];

        foreach ($students as $student) {
            $report[$student->id] = [
                'name' => $student->name,
                'class' => $student->classroom->name,
                'attendance' => $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate)
            ];
        }


        return $report;


        // $students = student::where('classroom_id', $classroomId)->get();
        // $report = [];

        // foreach ($students as $student) {
        //     $report[$student->id] = [
        //         'name' => $student->name,
        //         'attendance' => $this->generateStudentAttendanceSummary($student->id, $startDate, $endDate)
        //     ];
        // }

        // return $report;
    }

    public function generateStudentAttendanceSummary($studentId, $startDate, $endDate)
    {
        $dateRange = CarbonPeriod::create($startDate, $endDate);
        $summary = [];

        foreach ($dateRange as $date) {
            $dailySummary = $this->aggregateDailyAttendance($date->format('Y-m-d'), $studentId);
            $summary[$date->format('Y-m-d')] = $dailySummary;
        }

        return $summary;
    }
}
