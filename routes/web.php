<?php

use App\Http\Controllers\Admin\absencePointController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AttendanceReportController;
use App\Http\Controllers\admin\AttendanceStudentController;
use App\Http\Controllers\admin\AttendanceTeacherController;
use App\Http\Controllers\admin\classroomController;
use App\Http\Controllers\admin\classroomTeacherController;
use App\Http\Controllers\admin\courseController;
use App\Http\Controllers\Admin\dashboardController;
use App\Http\Controllers\Admin\ExamLoginController;
use App\Http\Controllers\Admin\installmentsPaymentController;
use App\Http\Controllers\Admin\kbmPeriodController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\admin\permissionController;
use App\Http\Controllers\admin\promotedStudentController;
use App\Http\Controllers\Admin\scheduleController;
use App\Http\Controllers\admin\settingController;
use App\Http\Controllers\Admin\SIAController;
use App\Http\Controllers\admin\studentController;
use App\Http\Controllers\Admin\StudentPaymentController;
use App\Http\Controllers\admin\teacherController;
use App\Http\Controllers\admin\teachingHourController;
use App\Http\Controllers\Admin\TimescheduleController;
use App\Http\Controllers\Admin\TypeClassController;
use App\Http\Controllers\auth\authController;
use App\Http\Controllers\student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\student\studentDashboardController;
use App\Http\Controllers\StudentShip\dashboardController as StudentShipDashboardController;
use App\Http\Controllers\Teacher\AttendaceController;
use App\Http\Controllers\Teacher\AttendaceStudentController;
use App\Http\Controllers\Teacher\DashboardController as ControllersTeacherDashboardController;
use App\Http\Controllers\teacher\HistoryAttendaceController;
use App\Http\Controllers\teacher\HistoryAttendaceStudentController;
use App\Http\Controllers\teacher\reportAttendanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('welcome');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::post('change-password', [authController::class, 'UpdatePass'])->name('change.password');
});

Route::middleware(['auth', 'role:admin'])->controller(dashboardController::class)->name('admin.')->group(function () {
    Route::get('dashboard-admin', 'index')->name('dashboard_admin');

    Route::resources([
        'teacher' => teacherController::class,
        'course' => courseController::class,
        'class_room' => classroomController::class,
        'student' => studentController::class,
        'teaching_hour' => teachingHourController::class,
        'classroom_teacher' => classroomTeacherController::class,
        'userAdmin' => AdminController::class,
        'schedule' => scheduleController::class,
        'attendance_report' => AttendanceReportController::class,
        'attendance_student' => AttendanceStudentController::class,
        'report_attendance_teacher' => AttendanceTeacherController::class,
        'time_schedule' => TimescheduleController::class,
        'type_class' => TypeClassController::class,
        'payment' => PaymentController::class,
        'student_payment' => StudentPaymentController::class,
        'setting' => settingController::class,
        'kbm_period' => kbmPeriodController::class,
        'absence_point' => absencePointController::class,
        'SIA' => SIAController::class,
        'permission' => permissionController::class,
        'installment' => installmentsPaymentController::class,
        'examLogin' => ExamLoginController::class
    ]);

    Route::get('/promoted/student', [promotedStudentController::class, 'promoted'])->name('promoted_student');
    Route::post('/promoted/student/update', [promotedStudentController::class, 'update'])->name('promoted_student.update');

    Route::get('/admin/student-payment', [StudentPaymentController::class, 'getStudentsByClassroom'])->name('studentPayment.get');
    Route::get('/attendance/results', [AttendanceStudentController::class, 'showResults'])->name('attendance.results');
    Route::get('search-student', [AttendanceStudentController::class, 'search'])->name('attendance_student.search');
    Route::get('report-search-student', [AttendanceReportController::class, 'search'])->name('report.attendance_student.search');
    Route::get('report-search-SIA', [SIAController::class, 'search'])->name('SIA.search');
    Route::get('export-report-attendance-excel', [AttendanceReportController::class, 'exportExcel'])->name('export.attendance_report.excel');
    Route::get('export-report-attendance-pdf', [AttendanceReportController::class, 'exportPdf'])->name('export.attendance_report.pdf');
    Route::get('export-report-SIA', [SIAController::class, 'export'])->name('SIA.export');

    Route::post('test-update-attendance', [AttendanceReportController::class, 'updateAttendanceRequest'])->name('updateAttendanceRequest');
    Route::post('classroom-import', [classroomController::class, 'import'])->name('classroom.import');
    Route::post('student-import', [studentController::class, 'import'])->name('student.import');
    Route::post('teacher-import', [teacherController::class, 'import'])->name('teacher.import');
    Route::post('examLogin-import', [ExamLoginController::class, 'import'])->name('examLogin.import');
    Route::post('report-search-student', [AttendanceReportController::class, 'update'])->name('report.attendance_student.update');
    Route::post('api-installments', [installmentsPaymentController::class, 'api'])->name('installments.api');

    Route::put('setting-update', [settingController::class, 'update'])->name('setting.update');
    Route::put('attendance_student/{id}', [AttendanceStudentController::class, 'update'])->name('admin.attendance_student.update');
});

Route::middleware(['auth', 'role:studentShip'])->controller(StudentShipDashboardController::class)->name('studentShip.')->group(function () {
    Route::get('dashboard-studentShip', 'index')->name('dashboard_studentShip');
});

Route::middleware(['auth', 'role:student'])->controller(studentDashboardController::class)->name('student.')->group(function () {
    Route::get('dashboard-student', 'index')->name('dashboard_student');

    Route::resources([
        'schedule_student' => StudentScheduleController::class
    ]);

    Route::get('check-payment-student', [studentDashboardController::class, 'checkPaymentStudent'])->name('checkPaymentStudent');
    Route::post('permission', [StudentScheduleController::class, 'permission'])->name('schedule_student.permission');
});

Route::middleware(['auth', 'role:teacher'])->controller(ControllersTeacherDashboardController::class)->name('teacher.')->group(function () {
    Route::get('dashboard-teacher', 'index')->name('dashboard_teacher');

    Route::resources([
        'attendance_teacher' => AttendaceController::class,
        'history_attendance' => HistoryAttendaceController::class,
        'attendance_homeroom' => reportAttendanceController::class
    ]);

    Route::get('export-report-attendance-homeroom/{id}', [reportAttendanceController::class, 'export'])->name('report.attendance_homeroom.export');
    Route::get('attendance/{classroomid}/{scheduleId}', [AttendaceStudentController::class, 'index'])->name('attendance');
    Route::get('history-attendance/{classroomid}/{scheduleId}', [HistoryAttendaceStudentController::class, 'index'])->name('attendance.history');
    Route::post('attendance-student/{scheduleId}', [AttendaceStudentController::class, 'store'])->name('attendance.store');
    Route::post('attendance-student-history/{scheduleId}', [AttendaceStudentController::class, 'update'])->name('attendance.update.history');
});
