<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Attendance\StampController;
use App\Http\Controllers\Attendance\ListController as AttendanceListController;
use App\Http\Controllers\Attendance\DetailController;
use App\Http\Controllers\Application\ListController as ApplicationListController;
use App\Http\Controllers\Application\DetailController as ApplicationDetailController;
use App\Http\Controllers\Application\SubmitController;
use App\Http\Controllers\Admin\Application\ApplicationListController as AdminApplicationListController;
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
Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
Route::get('/attendance/{id}', [DetailController::class, 'show'])->name('attendance.show')->middleware('auth');
Route::get('/application/list', [ApplicationListController::class, 'index'])->name('application.list');
Route::middleware(['auth'])->group(function () {
  Route::get('/application/{id}', [ApplicationDetailController::class, 'show'])->name('application.detail');

  //申請フォーム表示
  Route::get('/application/create/{attendance_id}', [SubmitController::class, 'create'])->name('application.create');
  //申請送信処理
  Route::post('application/store',[SubmitController::class, 'store'])->name('application.store');
  Route::get('/admin/application/list', [AdminApplicationListController::class, 'index'])
    ->name('admin.application.list');
});

// 管理者：日別勤怠一覧
Route::get('/admin/attendance', [DailyListController::class, 'index'])->name('admin.attendance.index');
// 管理者：勤怠詳細
Route::get('/admin/attendance/{id}', [DetailController::class, 'show'])->name('admin.attendance.detail');
