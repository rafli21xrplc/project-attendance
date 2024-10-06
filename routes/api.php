<?php

use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\paymentController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\auth\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [authController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

// teacher
Route::get('teacher-data', [AttendanceController::class, 'getTeacherData']);
Route::get('schedule', [AttendanceController::class, 'getSchedule']);
Route::get('report-attendance/{id}/{month}', [AttendanceController::class, 'getAttendance']);
Route::get('attendance-special-day', [AttendanceController::class, 'getSpecialDay']);
Route::post('attendance/{id}', [AttendanceController::class, 'store']);
Route::put('attendance', [AttendanceController::class, 'update']);
Route::post('store-attendance/specialDay', [AttendanceController::class, 'storeSpecialDay']);
Route::put('update-attendance/specialDay', [AttendanceController::class, 'updateSpecialDay']);

// student
Route::get('permission-student/{id}', [StudentController::class, 'getPermission']);
Route::get('schedule-student', [StudentController::class, 'schedule']);
Route::post('permission/{id}', [StudentController::class, 'storePermission']);
Route::get('log-in/{id}', [StudentController::class, 'in']);
Route::get('log-out/{id}', [StudentController::class, 'out']);

// parent
Route::get('installment/{id}', [paymentController::class, 'getInstallment']);
Route::get('payment/{id}', [paymentController::class, 'getPayment']);
Route::get('attendance-student/{id}', [StudentController::class, 'getAttendanceStudent']);