<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SIAreportByClassExport;
use App\Exports\SIAreportExport;
use App\Http\Controllers\Controller;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\type_class;
use App\Traits\SIATrait;
use Carbon\Carbon;
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
        $type = type_class::with('classrooms')->get();
        $report = $this->getSIALaporan(['7b994382-55d3-3f0b-8adb-93624cf2c12c', 'c5723342-334e-3fc1-bc4d-a9c680029f25']);
        return view('admin.SIAreport')->with([
            'period' => $report['period'] ?? null,
            'report' => $report['report'] ?? null,
            'months' => $report['months'] ?? null,
            'classrooms' => $classroom,
            'types' => $type
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
        $type = type_class::with('classrooms')->get();
        $classroom = $this->getClassroom();
        $report = $this->getSIALaporanBySearch($request->states);
        return view('admin.SIAreport')->with([
            'period' => $report['period'],
            'report' => $report['report'],
            'months' => $report['months'],
            'classrooms' => $classroom,
            'types' => $type
        ]);
    }

    public function export(Request $request)
    {
        $ClassroomIds = classRoom::pluck('id')->toArray();
        $currentPeriod = kbm_period::getCurrentPeriod();

        if (!$currentPeriod) {
            return response()->json(['message' => 'No active KBM period found.'], 404);
        }

        $startDate = Carbon::parse($currentPeriod->start_date);
        $endDate = Carbon::parse($currentPeriod->end_date);
        $months = [];

        while ($startDate->lte($endDate)) {
            $months[] = $startDate->format('Y-m');
            $startDate->addMonth();
        }

        $reportData = $this->getSIALaporanExcel($ClassroomIds, $currentPeriod->id, $months);

        return Excel::download(new SIAreportByClassExport($reportData, $months, $currentPeriod), 'SIA_Report.xlsx');
    }
}
