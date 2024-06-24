<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendancReporteRequest;
use App\Models\attendance;
use App\Models\kbm_period;
use App\Models\type_class;
use Illuminate\Support\Str;
use App\Traits\AttendanceTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportController extends Controller
{

    use AttendanceTrait;
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
    public function update(Request $request)
    {

        $studentId = $request->input('student_id');
        $date = $request->input('date');
        $newInput = $request->input('content');

        $existingData = $this->getAttendanceData($studentId, $date);

        $newStatusCounts = $this->parseInput($newInput);

        $changes = $this->determineChanges($existingData, $newStatusCounts);

        $result = $this->applyChanges($studentId, $date, $changes);

        return response()->json(['success' => true]);

    }

    private function convertStatusToIndonesian($status)
    {
        switch ($status) {
            case 'A':
                return 'alpha';
            case 'H':
                return 'present';
            case 'I':
                return 'permission';
            case 'S':
                return 'sick';
            default:
                return $status;
        }
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

    public function exportExcel()
    {
        $TypeIds = type_class::pluck('id')->toArray(); // value X, XI, XII
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        return Excel::download(new AttendanceExport($TypeIds, $startDate, $endDate), 'class_attendance_report.xlsx');
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '1G');

        $TypeIds = type_class::pluck('id')->toArray(); // value X, XI, XII
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();


        $attendanceExport = new AttendanceExportPdf($TypeIds, $startDate, $endDate);

        $pdf = PDF::loadView('exports.report_attendance_pdf', ['attendanceExport' => $attendanceExport])
            ->setPaper('a3', 'landscape');

        return $pdf->download('class_attendance_report.pdf');
    }
}
