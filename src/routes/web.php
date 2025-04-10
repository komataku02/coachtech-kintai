<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Attendance\StampController;
use App\Http\Controllers\Attendance\ListController;
use App\Http\Controllers\Attendance\DetailController;
use App\Http\Controllers\Admin\Attendance\DailyListController;



Route::get('/register',[RegisterController::class, 'create'])->name('register');
Route::post('/register',[RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/attendance', [StampController::class, 'index'])->name('attendance.index');
Route::middleware(['auth'])->group(function () {
  Route::post('/attendance/clock-in', [StampController::class, 'clockIn'])->name('attendance.clockIn');
  Route::post('/attendance/clock-out', [StampController::class, 'clockOut'])->name('attendance.clockOut');
  Route::post('/attendance/break-in', [StampController::class, 'breakIn'])->name('attendance.breakIn');
  Route::post('/attendance/break-out', [StampController::class, 'breakOut'])->name('attendance.breakOut');
});
Route::get('/attendance/list', [ListController::class, 'index'])->name('attendance.list');
Route::get('/attendance/{id}', [DetailController::class, 'show'])->name('attendance.show')->middleware('auth');
// 管理者：日別勤怠一覧
Route::get('/admin/attendance', [DailyListController::class, 'index'])->name('admin.attendance.index');
// 管理者：勤怠詳細
Route::get('/admin/attendance/{id}', [DetailController::class, 'show'])->name('admin.attendance.detail');
