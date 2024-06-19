<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Exports\reportAttendanceExport;
use App\Exports\singleClassAttendanceExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendancReporteRequest;
use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\student;
use App\Models\type_class;
use Illuminate\Support\Str;
use App\Traits\AttendanceTrait;
use Carbon\CarbonPeriod;
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

        $validatedData = $request->validate([
            'student_id' => 'required|exists:student,id',
            'date' => 'required|date',
            'content' => 'required|string',
            'attendances' => 'required|array'
        ]);

        try {
            $studentId = $validatedData['student_id'];
            $statusString = $validatedData['content'];
            $attendances = $validatedData['attendances'];

            preg_match_all('/(\d+)([A-Za-z]+)/', $statusString, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $hours = $match[1];
                $status = strtoupper($match[2]);

                $statusKey = strtolower($this->convertStatusToIndonesian($status));

                if (isset($attendances[$statusKey]) && !empty($attendances[$statusKey])) {
                    foreach ($attendances[$statusKey] as $timeIndex => $time) {
                        $attendanceId = $attendances[$statusKey][$timeIndex] ?? null;
                        if ($attendanceId && $hours && $statusKey) {
                            $attendance = attendance::find($attendanceId);
                            if ($attendance) {
                                $attendance->update([
                                    'update_at' => now(),
                                    'status' => $statusKey,
                                    'hours' => $hours,
                                ]);
                            }
                        } else {
                            attendance::create([
                                'id' => Str::uuid(),
                                'student_id' => $studentId,
                                'kbm_period_id' => kbm_period::getCurrentPeriod()->id,
                                'schedule_id' => $this->getScheduleId($studentId, $time),
                                'time' => $time,
                                'status' => $statusKey,
                                'hours' => $hours,
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        return response()->json(['success' => true, 'message' => 'Kehadiran berhasil diperbarui!']);
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

    public function export()
    {
        $TypeIds = type_class::pluck('id')->toArray();
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        return Excel::download(new reportAttendanceExport($TypeIds, $startDate, $endDate), 'class_attendance_report.xlsx');
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
