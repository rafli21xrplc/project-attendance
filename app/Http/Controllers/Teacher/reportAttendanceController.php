<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Models\classRoom;
use App\Services\AttendanceHomeroomPdf;
use App\Traits\AttendanceTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class reportAttendanceController extends Controller
{
    use AttendanceTrait;

    private AttendanceHomeroomPdf $attendanceHomeroomPdf;

    public function __construct(AttendanceHomeroomPdf $attendanceHomeroomPdf)
    {
        $this->attendanceHomeroomPdf = $attendanceHomeroomPdf;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classroom_id = auth()->user()->teacher->classroom->id;
        $report = $this->AttendanceHomeroom($classroom_id);
        return view('teacher.report_attendance_homeroom')->with([
            'report' => $report['report'],
            'classroom' => $report['classroom'],
            'startDate' => $report['startDate'],
            'endDate' => $report['endDate'],
        ]);
    }

    public function export(string $classroom_id)
    {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        $classroom = classRoom::with(['students.attendance', 'typeClass', 'teacher'])
            ->findOrFail($classroom_id);

        $attendanceSummary = $this->attendanceHomeroomPdf->generateAttendanceSummary($classroom, $startDate, $endDate);
        $dateRange = CarbonPeriod::create($startDate, $endDate);

        $pdf = Pdf::loadView('exports.reportAttendanceHomeTeacher', compact('classroom', 'attendanceSummary', 'dateRange', 'startDate', 'endDate'))->setPaper('a3', 'landscape');

        return $pdf->download('class_attendance_report.pdf');
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
}
