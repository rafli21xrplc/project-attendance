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
use App\Http\Controllers\admin\subtractionTimeController;
use App\Http\Controllers\admin\teacherController;
use App\Http\Controllers\admin\teachingHourController;
use App\Http\Controllers\Admin\TimescheduleController;
use App\Http\Controllers\admin\timeScheduleDayController;
use App\Http\Controllers\Admin\TypeClassController;
use App\Http\Controllers\admin\typePaymentController;
use App\Http\Controllers\auth\authController;
use App\Http\Controllers\student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\student\studentDashboardController;
use App\Http\Controllers\StudentShip\dashboardController as StudentShipDashboardController;
use App\Http\Controllers\Teacher\AttendaceController;
use App\Http\Controllers\Teacher\AttendaceStudentController;
use App\Http\Controllers\teacher\attendanceSpesialDayController;
use App\Http\Controllers\Teacher\DashboardController as ControllersTeacherDashboardController;
use App\Http\Controllers\teacher\HistoryAttendaceController;
use App\Http\Controllers\teacher\HistoryAttendaceStudentController;
use App\Http\Controllers\teacher\reportAttendanceController;
use App\Http\Controllers\teacher\reportAttendanceTeacherController;
use App\Models\payment;
use App\Models\student;
use App\Models\teacher;
use App\Models\time_schedule_day;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;

Route::get('/', function () {
    return view('auth.login');
})->name('welcome');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::post('change-profile', [authController::class, 'UpdateProfile'])->name('change.profile');
});

Route::middleware(['auth', 'role:admin'])->controller(dashboardController::class)->name('Admin.')->group(function () {
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
        'examLogin' => ExamLoginController::class,
        'type_payment' => typePaymentController::class,
        'subtraction' => subtractionTimeController::class
    ]);

    Route::post('payment-import', [PaymentController::class, 'import'])->name('payment.import');

    Route::get('attendance-student-report', [AttendanceReportController::class, 'student'])->name('attendance.student.report');
    Route::post('update-attendance-student-report', [AttendanceReportController::class, 'update'])->name('update.attendance.student.report');
    Route::get('student-search', [studentController::class, 'search'])->name('student.search');

    Route::get('export-report-attendance-teacher-search', [AttendanceTeacherController::class, 'search'])->name('report.attendance_teacher_late.search');
    Route::post('export-report-attendance-teacher-export', [AttendanceTeacherController::class, 'export'])->name('export.attendance_report_teacher.date');

    Route::get('export-report-attendance-teacher-pdf', [AttendanceTeacherController::class, 'exportPdf'])->name('report.attendance_teacher_late.pdf');
    Route::post('export-report-attendance-teacher-range-date-pdf', [AttendanceTeacherController::class, 'exportRangeDatePdf'])->name('report.attendance_teacher_late.range.date.pdf');

    Route::get('promoted-student', [promotedStudentController::class, 'promoted'])->name('promoted_student');
    Route::post('promoted-student-update', [promotedStudentController::class, 'update'])->name('promoted_student.update');

    Route::post('setting-spesial-day', [settingController::class, 'spesialDay'])->name('setting.spesialDay');

    Route::get('student-payment', [StudentPaymentController::class, 'getStudentsByClassroom'])->name('studentPayment.get');
    Route::get('attendance-results', [AttendanceStudentController::class, 'showResults'])->name('attendance.results');
    Route::get('search-student', [AttendanceStudentController::class, 'search'])->name('attendance_student.search.report');
    Route::get('report-search-student', [AttendanceReportController::class, 'search'])->name('report.attendance_student.search');
    Route::get('report-search-SIA', [SIAController::class, 'search'])->name('SIA.search');
    Route::get('export-report-attendance-pdf', [AttendanceReportController::class, 'export'])->name('export.attendance_report.pdf');
    Route::get('export-report-SIA', [SIAController::class, 'export'])->name('SIA.export');
    Route::get('export-report-attendance-excel', [AttendanceReportController::class, 'exportExcel'])->name('export.attendance_report.excel');
    Route::get('export-payment/{id}', [PaymentController::class, 'export'])->name('export.payment');
    Route::get('export-payment-rekapitulasi', [PaymentController::class, 'exportRekapitulasi'])->name('export.payment.rekapitulasi');
    
    Route::post('export-payment-installment', [PaymentController::class, 'exportInstallment'])->name('export.payment.installment');
    Route::post('export-report-attendance-excel-by-month', [AttendanceReportController::class, 'exportExcelMonth'])->name('export.attendance_report.excel.month');
    Route::post('export-report-permission-pdf', [permissionController::class, 'exportPdf'])->name('export.permission.pdf');
    
    Route::post('export-report-bbpp-excel', [StudentPaymentController::class, 'export'])->name('export.payment_bbpp.excel');

    Route::post('export-report-attendance-excel-by-month', [AttendanceReportController::class, 'exportOption'])->name('export.attendance_report.month');

    Route::post('test-update-attendance', [AttendanceReportController::class, 'updateAttendanceRequest'])->name('updateAttendanceRequest');
    Route::post('classroom-import', [classroomController::class, 'import'])->name('classroom.import');
    Route::post('student-import', [studentController::class, 'import'])->name('student.import');
    Route::post('teacher-import', [teacherController::class, 'import'])->name('teacher.import');
    Route::post('examLogin-import', [ExamLoginController::class, 'import'])->name('examLogin.import');
    Route::post('report-search-student', [AttendanceReportController::class, 'update'])->name('report.attendance_student.update');
    Route::post('api-installments', [installmentsPaymentController::class, 'api'])->name('installments.api');

    Route::put('setting-update', [settingController::class, 'update'])->name('setting.update');
    Route::put('attendance_student/{id}', [AttendanceStudentController::class, 'update'])->name('Admin.attendance_student.update');
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
        'report_attendance' => reportAttendanceTeacherController::class,
        'attendance_homeroom' => reportAttendanceController::class,
        'attendance_spesial_day' => attendanceSpesialDayController::class
    ]);

    Route::post('export-report-attendance-homeroom/{id}', [reportAttendanceController::class, 'export'])->name('report.attendance_homeroom.export');

    Route::get('attendance/{classroomid}/{scheduleId}', [AttendaceStudentController::class, 'index'])->name('attendance');
    Route::get('history-attendance/{classroomid}/{scheduleId}', [HistoryAttendaceStudentController::class, 'index'])->name('attendance.history');
    Route::post('attendance-student/{scheduleId}', [AttendaceController::class, 'store'])->name('attendance.store');
    Route::post('attendance-student-history/{scheduleId}', [AttendaceStudentController::class, 'update'])->name('attendance.update.history');
    Route::post('attendance-teacher-homeroom/{classroomId}', [attendanceSpesialDayController::class, 'store'])->name('attendance_spesial_day.store');

    Route::post('search-attendance', [HistoryAttendaceController::class, 'search'])->name('attendance.search');
});

Route::get('/fix-semua-siswa', function () {
    // Matikan batas waktu eksekusi agar tidak RTO saat memproses banyak data
    set_time_limit(0);

    // 1. PASTI AMAN: Pastikan role 'student' sudah terdaftar di database
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

    // 2. Ambil semua data langsung dari tabel students
    $students = student::all();
    
    $berhasil = 0;

    foreach ($students as $student) {
        // 3. Cari akun user yang berelasi dengan student ini
        $user = User::find($student->user_id);

        if ($user) {
            // 4. Ubah password menjadi NISN (yang tersimpan di kolom username)
            $user->password = Hash::make($user->username);
            $user->save();

            // 5. Pasangkan role 'student'
            if (!$user->hasRole('student')) {
                $user->assignRole('student');
            }
            $berhasil++;
        }
    }

    return "<h1>Selesai! 🚀</h1>
            <p>Berhasil memperbaiki password dan role untuk <b>{$berhasil}</b> akun siswa.</p>
            <hr>
            <p><b>Silakan tes login!</b><br>
            Username: <i>(NISN Siswa)</i><br>
            Password: <i>(Sama dengan NISN)</i></p>";
});

Route::get('/fix-semua-guru', function () {
    // Matikan batas waktu eksekusi agar tidak RTO (Request Timeout) saat looping banyak data
    set_time_limit(0);

    // 1. PASTI AMAN: Pastikan role 'teacher' ada di database sebelum looping
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

    // 2. Ambil semua data dari table teacher
    $teachers = teacher::all(); 
    
    $berhasil = 0;
    $gagal = 0;

    foreach ($teachers as $teacher) {
        // 3. Cari user terkait. 
        // Asumsi: tabel teachers berelasi dengan tabel users menggunakan kolom 'user_id'
        $user = User::find($teacher->user_id);

        if ($user) {
            // 4. Ubah password menjadi sama dengan username (sesuai request Anda)
            $user->password = Hash::make($user->username);
            $user->save();

            // 5. Pasangkan role 'teacher'
            if (!$user->hasRole('teacher')) {
                $user->assignRole('teacher');
            }
            $berhasil++;
        } else {
            $gagal++;
        }
    }

    return "<h1>Selesai! 🚀</h1>
            <p>Berhasil memperbaiki password dan role untuk <b>{$berhasil}</b> akun guru.</p>
            <p>Data guru yang tidak ditemukan akun usernya: <b>{$gagal}</b> (Silakan cek relasi user_id).</p>
            <hr>
            <p><b>Catatan:</b><br> 
            Semua guru sekarang dapat login menggunakan:<br>
            Username: <i>(Username/NIP masing-masing)</i><br>
            Password: <i>(Sama dengan Username)</i></p>";
});

Route::get('/test-users', [App\Http\Controllers\TestUserController::class, 'index'])->name('test.users');