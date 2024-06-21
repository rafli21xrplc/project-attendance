<?php

namespace App\Http\Controllers\teacher;

use App\Exports\ClassAttendanceHomeroomExport;
use App\Http\Controllers\Controller;
use App\Traits\AttendanceTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class reportAttendanceController extends Controller
{
    use AttendanceTrait;

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

    public function export(string $id)
    {
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        return Excel::download(new ClassAttendanceHomeroomExport($id, $startDate, $endDate), 'class_attendance_report.xlsx');
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
