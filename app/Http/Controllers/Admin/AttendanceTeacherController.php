<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\AttendanceTeacherInterface;
use App\Exports\AttendanceLateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\attendanceLateRequest;
use App\Models\attendanceLate;
use App\Traits\TeacherTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceTeacherController extends Controller
{

    use TeacherTrait;

    private AttendanceTeacherInterface $attendanceTeacher;
    public function __construct(AttendanceTeacherInterface $attendanceTeacher)
    {
        $this->attendanceTeacher = $attendanceTeacher;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teacher = $this->attendanceLate();
        return view('admin.attendanceTeacher', compact('teacher'));
    }

    public function search(Request $request)
    {
        $teacher = attendanceLate::attendanceLateByDate($request->start_date, $request->end_date);
        return view('admin.attendanceTeacher', compact('teacher'));
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '1G');

        $todayFormat = Carbon::today();
        $teacherLate = attendanceLate::attendanceLateByDateToday($todayFormat);

        $pdf = Pdf::loadView('exports.report_attendance_late_pdf', [
            'day' => $todayFormat,
            'teacher_late' => $teacherLate
        ])->setPaper('a3', 'landscape');

        return $pdf->download('schedule_late.pdf');
    }

    public function exportRangeDatePdf(attendanceLateRequest $request)
    {
        ini_set('memory_limit', '1G');

        $teacherLate = attendanceLate::attendanceLateByDate($request->start_date, $request->end_date);

        $pdf = Pdf::loadView('exports.report_attendance_late_range_date_pdf', [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'teacher_late' => $teacherLate
        ])->setPaper('a3', 'landscape');

        return $pdf->download('schedule_late.pdf');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1G');

        if ($request->format == 'pdf') {
            $teacherLate = attendanceLate::attendanceLateByDate($request->start_date, $request->end_date);

            $pdf = Pdf::loadView('exports.report_attendance_late_range_date_pdf', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'teacher_late' => $teacherLate
            ])->setPaper('a3', 'landscape');

            return $pdf->download('schedule_late.pdf');
        } else if ($request->format == 'excel') {
            return Excel::download(new AttendanceLateExport($request->start_date, $request->end_date), 'schedule_late.xlsx');
        }
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
