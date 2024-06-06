<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AttendanceClassroomController;
use App\Http\Controllers\admin\attendanceRekapController;
use App\Http\Controllers\admin\AttendanceReportController;
use App\Http\Controllers\admin\AttendanceStudentController;
use App\Http\Controllers\admin\AttendanceTeacherController;
use App\Http\Controllers\admin\classroomController;
use App\Http\Controllers\admin\classroomTeacherController;
use App\Http\Controllers\admin\courseController;
use App\Http\Controllers\Admin\dashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\scheduleController;
use App\Http\Controllers\admin\studentController;
use App\Http\Controllers\Admin\StudentPaymentController;
use App\Http\Controllers\admin\teacherController;
use App\Http\Controllers\admin\teachingHourController;
use App\Http\Controllers\Admin\TimescheduleController;
use App\Http\Controllers\Admin\TypeClassController;
use App\Http\Controllers\coordinator\dashboardController as CoordinatorDashboardController;
use App\Http\Controllers\officer\dashboardController as OfficerDashboardController;
use App\Http\Controllers\student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\student\studentDashboardController;
use App\Http\Controllers\StudentShip\dashboardController as StudentShipDashboardController;
use App\Http\Controllers\Teacher\AttendaceController;
use App\Http\Controllers\Teacher\AttendaceStudentController;
use App\Http\Controllers\Teacher\DashboardController as ControllersTeacherDashboardController;
use App\Http\Controllers\teacher\HistoryAttendaceController;
use App\Http\Controllers\teacher\HistoryAttendaceStudentController;
use App\Http\Controllers\teacherDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('dashboard-admin', function () {
//     return view('Admin.dashboard');
// })->name('dashboard');
// Route::get('dashboard-petugas', function () {
//     return view('Petugas.dashboard');
// })->name('dashboard');
// Route::get('dashboard-kesiswaan', function () {
//     return view('kesiswaan.dashboard');
// })->name('dashboard');
// Route::get('dashboard-koordinator', function () {
//     return view('Koordinator.dashboard');
// })->name('dashboard');
// Route::get('dashboard-kepala-sekolah', function () {
//     return view('KepalaSekolah.dashboard');
// })->name('dashboard');

Route::get('/testing', function () {
    return view('testing');
});

Route::get('user', function () {
    return view('Admin.user');
})->name('user.view');

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
        // 'attendance_teacher' => AttendanceTeacherController::class,
        // 'attedance_report' => AttendanceClassroomController::class,
        'attendance_report' => AttendanceReportController::class,
        'attendance_student' => AttendanceStudentController::class,
        'time_schedule' => TimescheduleController::class,
        'type_class' => TypeClassController::class,
        'payment' => PaymentController::class,
        'student_payment' => StudentPaymentController::class
    ]);

    Route::get('/attendance/results', [AttendanceStudentController::class, 'showResults'])->name('attendance.results');
    Route::put('attendance_student/{id}', [AttendanceStudentController::class, 'update'])->name('admin.attendance_student.update');
    Route::get('export-report-attendance', [AttendanceReportController::class, 'export'])->name('export.attendance_report');
    Route::post('search-student', [AttendanceStudentController::class, 'search'])->name('attendance_student.search');
    Route::post('report-search-student', [AttendanceReportController::class, 'search'])->name('report.attendance_student.search');
});

Route::middleware(['auth', 'role:studentShip'])->controller(StudentShipDashboardController::class)->name('studentShip.')->group(function () {
    Route::get('dashboard-studentShip', 'index')->name('dashboard_studentShip');
});

Route::middleware(['auth', 'role:student'])->controller(studentDashboardController::class)->name('student.')->group(function () {
    Route::get('dashboard-student', 'index')->name('dashboard_student');

    Route::resources([
        'schedule_student' => StudentScheduleController::class
    ]);

    Route::post('permission', [StudentScheduleController::class, 'permission'])->name('schedule_student.permission');
});

Route::middleware(['auth', 'role:teacher'])->controller(ControllersTeacherDashboardController::class)->name('teacher.')->group(function () {
    Route::get('dashboard-teacher', 'index')->name('dashboard_teacher');

    Route::resources([
        'attendance_teacher' => AttendaceController::class,
        'history_attendance' => HistoryAttendaceController::class
    ]);

    Route::get('attendance/{classroomid}/{scheduleId}', [AttendaceStudentController::class, 'index'])->name('attendance');
    Route::get('history-attendance/{classroomid}/{scheduleId}', [HistoryAttendaceStudentController::class, 'index'])->name('attendance.history');
    Route::post('attendance-student/{scheduleId}', [AttendaceStudentController::class, 'store'])->name('attendance.store');
    Route::post('attendance-student-history/{scheduleId}', [AttendaceStudentController::class, 'update'])->name('attendance.update.history');
});
