<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Attendance\StampController;
use App\Http\Controllers\Attendance\ListController as AttendanceListController;
use App\Http\Controllers\Attendance\DetailController;
use App\Http\Controllers\Application\ListController as ApplicationListController;
use App\Http\Controllers\Application\SubmitController;
use App\Http\Controllers\Admin\Application\ApplicationListController as AdminApplicationListController;
use App\Http\Controllers\Admin\Application\ApplicationDetailController as AdminApplicationDetailController;
use App\Http\Controllers\Admin\Attendance\DailyListController;
use App\Http\Controllers\Admin\Attendance\DetailController as AdminAttendanceDetailController;
use App\Http\Controllers\Admin\Staff\StaffListController;
use App\Http\Controllers\Admin\Staff\MonthlyAttendanceListController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;

/**
 * 一般ユーザー：認証前
 */
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
  Auth::logout();
  return redirect('/login');
})->name('logout');

/**
 * メール認証
 */
Route::get('/email/verify', fn() => view('auth.verify'))
  ->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();
  return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();
  return back()->with('message', '確認リンクを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/**
 * 一般ユーザー：認証済み
 */
Route::middleware(['auth', 'verified'])->group(function () {
  // PG03 勤怠登録画面
  Route::get('/attendance', [StampController::class, 'index'])->name('attendance.index');
  Route::post('/attendance/clock-in', [StampController::class, 'clockIn'])->name('attendance.clockIn');
  Route::post('/attendance/clock-out', [StampController::class, 'clockOut'])->name('attendance.clockOut');
  Route::post('/attendance/break-start', [StampController::class, 'breakIn'])->name('attendance.breakIn');
  Route::post('/attendance/break-end', [StampController::class, 'breakOut'])->name('attendance.breakOut');

  // PG04 勤怠一覧、PG05 勤怠詳細
  Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
  Route::get('/attendance/{id}', [DetailController::class, 'show'])->name('attendance.show');

  // PG06 申請一覧、修正申請登録
  Route::get('/stamp_correction_request/list', [ApplicationListController::class, 'index'])->name('application.list');
  Route::post('/stamp_correction_request/store', [SubmitController::class, 'store'])->name('application.store');
});

/**
 * 管理者：認証前
 */
Route::prefix('admin')->name('admin.')->group(function () {
  Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [AdminLoginController::class, 'login']);
  Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('admin.login');
  })->name('logout');
});

/**
 * 管理者：認証後
 */
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
  // PG08 勤怠一覧（管理者）
  Route::get('/attendance/list', [DailyListController::class, 'index'])->name('attendance.list');

  // PG09 勤怠詳細（管理者）
  Route::get('/attendance/{id}', [AdminAttendanceDetailController::class, 'show'])->name('attendance.detail');
  Route::put('/attendance/{id}/update', [AdminAttendanceDetailController::class, 'update'])->name('attendance.update');

  // PG12 申請一覧（管理者）
  Route::get('/stamp_correction_request/list', [AdminApplicationListController::class, 'index'])->name('application.list');

  // PG13 申請承認（管理者）
  Route::get('/stamp_correction_request/approve/{id}', [AdminApplicationDetailController::class, 'show'])->name('application.detail');
  Route::post('/stamp_correction_request/approve/{id}', [AdminApplicationDetailController::class, 'approve'])->name('application.approve');

  // PG10 スタッフ一覧
  Route::get('/staff/list', [StaffListController::class, 'index'])->name('staff.list');

  // PG11 スタッフ別勤怠
  Route::get('/attendance/staff/{id}', [MonthlyAttendanceListController::class, 'show'])->name('staff.attendance');
  Route::get('/attendance/staff/{id}/csv', [MonthlyAttendanceListController::class, 'downloadCsv'])->name('staff.attendance.csv');
});
