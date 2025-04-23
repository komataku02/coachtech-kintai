<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Attendance\StampController;
use App\Http\Controllers\Attendance\ListController as AttendanceListController;
use App\Http\Controllers\Attendance\DetailController;
use App\Http\Controllers\Application\ListController as ApplicationListController;
use App\Http\Controllers\Application\DetailController as ApplicationDetailController;
use App\Http\Controllers\Application\SubmitController;
use App\Http\Controllers\Admin\Application\ApplicationListController as AdminApplicationListController;
use App\Http\Controllers\Admin\Application\ApplicationDetailController as AdminApplicationDetailController;
use App\Http\Controllers\Admin\Attendance\DailyListController;
use App\Http\Controllers\Admin\Staff\StaffListController;
use App\Http\Controllers\Admin\Staff\MonthlyAttendanceListController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Attendance\DetailController as AdminAttendanceDetailController;

// -------------------- 認証（共通） --------------------
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', function () {
  Auth::logout();
  request()->session()->invalidate();
  request()->session()->regenerateToken();
  return redirect('/login');
})->name('logout');

// -------------------- 勤怠（一般ユーザー） --------------------
Route::middleware(['auth'])->group(function () {
  Route::get('/attendance', [StampController::class, 'index'])->name('attendance.index');
  Route::post('/attendance/clock-in', [StampController::class, 'clockIn'])->name('attendance.clockIn');
  Route::post('/attendance/clock-out', [StampController::class, 'clockOut'])->name('attendance.clockOut');
  Route::post('/attendance/break-in', [StampController::class, 'breakIn'])->name('attendance.breakIn');
  Route::post('/attendance/break-out', [StampController::class, 'breakOut'])->name('attendance.breakOut');

  Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
  Route::get('/attendance/{id}', [DetailController::class, 'show'])->name('attendance.show');
  // 👇 不要なので削除しました
  // Route::post('/attendance/{id}', [DetailController::class, 'update'])->name('attendance.update');
});

// -------------------- 申請（一般ユーザー） --------------------
Route::middleware(['auth'])->prefix('application')->name('application.')->group(function () {
  Route::get('/list', [ApplicationListController::class, 'index'])->name('list');
  Route::get('/{id}', [ApplicationDetailController::class, 'show'])->name('detail');
  Route::get('/create/{attendance_id}', [SubmitController::class, 'create'])->name('create');
  Route::post('/store', [SubmitController::class, 'store'])->name('store');
});

// -------------------- 管理者ルート --------------------
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
  // 管理者：申請管理
  Route::get('/application/list', [AdminApplicationListController::class, 'index'])->name('application.list');
  Route::get('/application/{id}', [AdminApplicationDetailController::class, 'show'])->name('application.detail');
  Route::post('/application/{id}/approve', [AdminApplicationDetailController::class, 'approve'])->name('application.approve');

  // 管理者：勤怠管理（日別）
  Route::get('/attendance', [DailyListController::class, 'index'])->name('attendance.index');
  Route::get('/attendance/{id}', [AdminAttendanceDetailController::class, 'show'])->name('attendance.detail');
  Route::put('/attendance/{id}', [AdminAttendanceDetailController::class, 'update'])->name('attendance.update');

  // 管理者：スタッフ管理
  Route::get('/staff/list', [StaffListController::class, 'index'])->name('staff.list');
  Route::get('/staff/{id}/attendance', [MonthlyAttendanceListController::class, 'show'])->name('staff.attendance');
  Route::get('/staff/{id}/attendance/csv', [MonthlyAttendanceListController::class, 'downloadCsv'])->name('staff.attendance.csv');
});

// 管理者ログイン画面
Route::prefix('admin')->name('admin.')->group(function () {
  Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
});
