<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('dashboard-admin', function () {
    return view('Admin.dashboard');
})->name('dashboard');
Route::get('dashboard-petugas', function () {
    return view('Petugas.dashboard');
})->name('dashboard');
Route::get('dashboard-kesiswaan', function () {
    return view('kesiswaan.dashboard');
})->name('dashboard');
Route::get('dashboard-koordinator', function () {
    return view('Koordinator.dashboard');
})->name('dashboard');
Route::get('dashboard-kepala-sekolah', function () {
    return view('KepalaSekolah.dashboard');
})->name('dashboard');
Route::get('user', function () {
    return view('Admin.user');
})->name('user.view');