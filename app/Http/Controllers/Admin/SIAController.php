<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SIAreportExport;
use App\Http\Controllers\Controller;
use App\Models\classRoom;
use App\Traits\SIATrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SIAController extends Controller
{
    use SIATrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classroom = $this->getClassroom();
        $report = $this->getSIALaporan(['7b994382-55d3-3f0b-8adb-93624cf2c12c', 'c5723342-334e-3fc1-bc4d-a9c680029f25']);
        return view('admin.SIAreport')->with([
            'period' => $report['period'],
            'report' => $report['report'],
            'months' => $report['months'],
            'classrooms' => $classroom
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

    public function search(Request $request)
    {
        $classroom = $this->getClassroom();
        $report = $this->getSIALaporanBySearch($request->classroom_id);
        return view('admin.SIAreport')->with([
            'period' => $report['period'],
            'report' => $report['report'],
            'classrooms' => $classroom
        ]);
    }

    public function export(Request $request)
    {
        $ClassroomIds = classRoom::pluck('id')->toArray();
        
        $reportData = $this->getSIALaporanExcel($ClassroomIds);

        $report = $reportData['report'];
        $months = $reportData['months'];
        $period = $reportData['period'];

        return Excel::download(new SIAReportExport($report, $months, $period), 'SIA_Report.xlsx');
    }
}
