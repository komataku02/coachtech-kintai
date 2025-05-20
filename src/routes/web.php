<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
  Auth::logout();
  return redirect('/login');
})->name('logout');

Route::get('/email/verify', function () {
  return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();
  return redirect()->route('attendance.index');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
  $request->user()->sendEmailVerificationNotification();
  return back()->with('message', '確認リンクを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/', [StampController::class, 'index'])->name('attendance.index');
  Route::post('/clock-in', [StampController::class, 'clockIn'])->name('attendance.clockIn');
  Route::post('/clock-out', [StampController::class, 'clockOut'])->name('attendance.clockOut');
  Route::post('/break-in', [StampController::class, 'breakIn'])->name('attendance.breakIn');
  Route::post('/break-out', [StampController::class, 'breakOut'])->name('attendance.breakOut');

  Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
  Route::get('/attendance/{id}', [DetailController::class, 'show'])->name('attendance.show');
  Route::post('/attendance/{id}/apply', [DetailController::class, 'update'])->name('attendance.apply');

  Route::get('/application/list', [ApplicationListController::class, 'index'])->name('application.list');
  Route::post('/application/store', [SubmitController::class, 'store'])->name('application.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
  Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
  Route::post('/login', [AdminLoginController::class, 'login']);
  Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('admin.login');
  })->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
  Route::get('/attendance', [DailyListController::class, 'index'])->name('attendance.index');
  Route::get('/attendance/{id}/detail', [AdminAttendanceDetailController::class, 'show'])->name('attendance.detail');
  Route::put('/attendance/{id}/update', [AdminAttendanceDetailController::class, 'update'])->name('attendance.update');

  Route::get('/application/list', [AdminApplicationListController::class, 'index'])->name('application.list');
  Route::get('/application/{id}', [AdminApplicationDetailController::class, 'show'])->name('application.detail');
  Route::post('/application/{id}/approve', [AdminApplicationDetailController::class, 'approve'])->name('application.approve');

  Route::get('/staff/list', [StaffListController::class, 'index'])->name('staff.list');
  Route::get('/staff/{id}/attendance', [MonthlyAttendanceListController::class, 'show'])->name('staff.attendance');
  Route::get('/staff/{id}/attendance/csv', [MonthlyAttendanceListController::class, 'downloadCsv'])->name('staff.attendance.csv');
});
