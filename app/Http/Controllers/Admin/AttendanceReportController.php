<?php

namespace App\Http\Controllers\admin;

use App\Contracts\Interfaces\AttendanceRekapInterface;
use App\Exports\attendanceAllStudentExport;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceExportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\attendance\SearchAttendancReporteRequest;
use App\Models\attendance;
use App\Models\classRoom;
use App\Models\kbm_period;
use App\Models\schedule;
use App\Models\student;
use App\Models\type_class;
use App\Services\AttendanceHomeroomPdf;
use Illuminate\Support\Str;
use App\Traits\AttendanceTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;


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
        $type = type_class::with('classrooms')->get();
        $students = $this->attendanceRekapInterface->getStudent();
        $classrooms = $this->attendanceRekapInterface->getClassroom();
        return view('admin.attendance_report', [
            'report' => null,
            'classroom' => null,
            'startDate' => null,
            'endDate' => null,
            'classrooms' => $classrooms,
            'types' => $type,
            'student' => $students
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

    // public function update(Request $request)
    // {
    //     return response()->json($request->all());

    //     // $this->updateAttendanceStudentReport($request->all());

    //     $this->updateAttendance($request->student_id, $request->date, $request->)

    //     try {

    //         return redirect()->back();
    //     } catch (\Throwable $th) {
    //         return redirect()->back();
    //     }
    // }

    public function update(Request $request)
    {
        $studentId = $request->input('student_id');
        $date = $request->input('date');
        $input = $request->input('content');
        
        $statusCounts = $this->parseInput($input);
        $schedules = $this->getSchedule($studentId, $date);
        
        
        if ($schedules->isEmpty()) {
            return response()->json(['message' => 'No schedule found for this student on the given date'], 404);
        }

        DB::beginTransaction();

        try {
            $updates = [];
            
            foreach ($statusCounts as $status => $hours) {
                foreach ($schedules as $item) {
                    $schedule = $this->getScheduleById($item->id);

                    $startSchedule = $schedule->StartTimeSchedules->time_number;
                    $endSchedule = $schedule->EndTimeSchedules->time_number;
                    $hour = ($endSchedule - $startSchedule) + 1;
                    
                    $updateData = $this->updateAttendance($studentId, $date, $status, $hour, $item->id);

                    if ($updateData) {
                        $updates[] = $updateData;
                    }
                }
            }

            foreach ($updates as $update) {
                Attendance::where('id', $update['id'])->update($update);
            }

            DB::commit();

            return response()->json(['message' => 'Attendance updated successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack(); 
            return response()->json(['message' => 'Error updating attendance: ' . $th->getMessage()], 500);
        }
    }


    private function updateAttendance($studentId, $date, $status, $hour, $scheduleId)
    {
        try {
            $attendance = Attendance::whereRaw('DATE(time) = ?', [$date])
            ->where('student_id', $studentId)
            ->where('schedule_id', $scheduleId)
            ->first();

            if ($attendance) {
                return [
                    'id' => $attendance->id,
                    'status' => $status,
                ];
            }

            return null;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function student(Request $request)
    {
        $studentId = $request->input('student_id');
        $date = $request->input('payment_date');

        $schedules = $this->getSchedule($studentId, $date);
        $data = []; // Initialize data array

        if (!$schedules->isEmpty()) {
            foreach ($schedules as $item) {
                $attendance = $this->updateAttendance($studentId, $date, $item->id);

                if ($attendance) {
                    $data[] = $attendance;
                }
            }
        }

        // Return JSON response
        return response()->json(['data' => $data]);
    }

    // private function updateAttendance($studentId, $date, $scheduleId)
    // {
    //     $attendance = DB::select("
    //         SELECT a.*, s.name as student_name 
    //         FROM attendance a
    //         JOIN student s ON a.student_id = s.id
    //         WHERE DATE(a.time) = ?
    //         AND a.student_id = ?
    //         AND a.schedule_id = ?
    //         LIMIT 1
    //     ", [$date, $studentId, $scheduleId]);

    //     try {


    //         return !empty($attendance) ? $attendance[0] : null;
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }

    private function insertAttendance($studentId, $date, $currentPeriod, $schedule, $startSchedule, $endSchedule, $hour)
    {
        try {

            $sql = '
            INSERT INTO attendance (id, student_id, schedule_id, kbm_period_id, time, hours, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ';

            $id = Str::uuid();

            DB::insert($sql, [
                $id,
                $studentId,
                $schedule->id,
                $currentPeriod->id,
                $date,
                $hour,
                'Present' // Default status during insertion
            ]);

            return $id;
        } catch (\Throwable $th) {
            throw $th; // Propagate the error to rollback the transaction
        }
    }

    private function updateAttendanceReport($id, $status)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            if ($attendance) {
                DB::update(
                    '
                UPDATE attendance 
                SET status = ?, updated_at = NOW() 
                WHERE id = ?',
                    [$status, $attendance->id]
                );
            }
        } catch (\Throwable $th) {
            throw $th; // Propagate the error to rollback the transaction
        }
    }

    private function getScheduleById($id)
    {
        return schedule::with(['StartTimeSchedules', 'EndTimeSchedules'])->where('id', $id)->first();
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
        $type = type_class::with('classrooms')->get();
        $students = $this->attendanceRekapInterface->getStudent();

        return view('admin.attendance_report', [
            'report' => $report['report'],
            'classroom' => $report['classroom'],
            'startDate' => $report['startDate'],
            'endDate' => $report['endDate'],
            'classrooms' => $classrooms,
            'types' => $type,
            'student' => $students
        ]);
    }

    public function exportExcel()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $TypeIds = type_class::pluck('id')->toArray(); // value X, XI, XII
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        return Excel::download(new AttendanceExport($TypeIds, $startDate, $endDate), 'class_attendance_report.xlsx');
    }

    public function exportPdf()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $TypeIds = type_class::pluck('id')->toArray(); // value X, XI, XII
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();


        $attendanceExport = new AttendanceExportPdf($TypeIds, $startDate, $endDate);


        $pdf = PDF::loadView('exports.report_attendance_pdf', ['attendanceExport' => $attendanceExport])
            ->setPaper('a3', 'landscape');

        return $pdf->download('class_attendance_report.pdf');
    }

    public function exportOption(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1G');

        $TypeIds = type_class::pluck('id')->toArray(); // value X, XI, XII

        $month = $request->input('month');
        $startDate = \Carbon\Carbon::parse($month)->startOfMonth()->toDateString();
        $endDate = \Carbon\Carbon::parse($month)->endOfMonth()->toDateString();


        if ($request->format == 'excel') {
            return Excel::download(new AttendanceExport($TypeIds, $startDate, $endDate), 'class_attendance_report.xlsx');
        } else if ($request->format == 'pdf') {

            $classrooms = classRoom::with(['students.attendance', 'typeClass', 'teacher'])->get();
            $pdfFiles = [];

            foreach ($classrooms as $classroom) {
                $attendanceSummary = $this->generateAttendanceSummaryReport($classroom, $startDate, $endDate);
                $dateRange = CarbonPeriod::create($startDate, $endDate);

                $pdf = PDF::loadView('exports.report_attendance_pdf', compact('classroom', 'attendanceSummary', 'dateRange', 'startDate', 'endDate'))
                    ->setPaper('a3', 'landscape');

                $filename = 'class_attendance_report_' . $classroom->typeClass->category . $classroom->name . '.pdf';
                $pdf->save(storage_path('app/public/attendance_reports/' . $filename));
                $pdfFiles[] = storage_path('app/public/attendance_reports/' . $filename);
            }

            return $this->downloadZip($pdfFiles, $month);
        } else {

            $typeClass = type_class::with('classrooms')->orderByRaw("FIELD(category, 'X', 'XI', 'XII', 'XIII')")->get();

            return Excel::download(new attendanceAllStudentExport($typeClass, $startDate, $endDate), 'class_attendance_report.xlsx');
        }
    }

    public function updateStudentStatus($studentId, $date, $input)
    {
        $statusCounts = $this->parseInput($input);
        $schedules = $this->getScheduleId($studentId, $date);

        $attendanceData = [];

        // Validasi dan siapkan data untuk update attendance
        foreach ($schedules as $schedule) {
            $startSchedule = $schedule->StartTimeSchedules->time_number;
            $endSchedule = $schedule->EndTimeSchedules->time_number;
            $hours = ($endSchedule - $startSchedule) + 1;

            foreach ($statusCounts as $status => $inputHours) {
                if ($inputHours == $hours) {
                    $attendanceData['attendance'][$schedule->id] = $status;
                }
            }
        }

        return $statusCounts;

        // Pastikan setiap schedule memiliki 1 attendance record
        foreach ($attendanceData['attendance'] as $scheduleId => $status) {
            $attendances = Attendance::where('schedule_id', $scheduleId)
                ->where('student_id', $studentId)
                ->whereDate('time', $date)
                ->get();

            if ($attendances->count() > 1) {
                $withUpdatedAt = $attendances->filter(function ($attendance) {
                    return !is_null($attendance->updated_at);
                });

                $withoutUpdatedAt = $attendances->filter(function ($attendance) {
                    return is_null($attendance->updated_at);
                });

                foreach ($withoutUpdatedAt as $attendance) {
                    $attendance->delete();
                }

                if ($withUpdatedAt->count() > 1) {
                    $withUpdatedAt->shift();
                    foreach ($withUpdatedAt as $attendance) {
                        $attendance->delete();
                    }
                }
            }
        }

        // Update attendance menggunakan updateAttendanceStudent
        return $this->updateAttendanceStudent($attendanceData);
    }

    public function updateAttendanceStudent(array $attendances)
    {
        try {
            $updates = [];

            foreach ($attendances['attendance'] as $scheduleId => $status) {
                $existingAttendance = Attendance::where('schedule_id', $scheduleId)->first();

                if (isset($existingAttendance)) {
                    if ($existingAttendance->status !== $status) {
                        $updates[] = [
                            'id' => $existingAttendance->id,
                            'status' => $status,
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            foreach ($updates as $update) {
                Attendance::where('id', $update['id'])->update($update);
            }

            return redirect()->back()->with('success', 'Attendance updated successfully');
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function parseInput($input)
    {
        preg_match_all('/(\d+)(\w)/', $input, $matches, PREG_SET_ORDER);
        $statusCounts = [];
        foreach ($matches as $match) {
            $hours = (int)$match[1];
            $status = $this->convertFormToShortStatus($match[2]);
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status] += $hours;
        }
        return $statusCounts;
    }

    private function getScheduleId($studentId, $date)
    {
        $student = student::find($studentId);
        if (!$student) {
            return null;
        }

        $classroomId = $student->classroom_id;
        $carbonDate = Carbon::parse($date);
        $dayName = $carbonDate->format('l');

        return schedule::where('classroom_id', $classroomId)
            ->where('day_of_week', $dayName)
            ->with(['StartTimeSchedules', 'EndTimeSchedules'])
            ->get();
    }
}
