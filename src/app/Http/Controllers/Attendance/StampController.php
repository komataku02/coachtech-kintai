<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class StampController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        return view('attendance.index', compact('user', 'attendance'));
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('attendance.index')->with('message', '本日はすでに出勤済みです。');
        }

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today,
            'clock_in_time' => now()->format('H:i:s'),
            'status' => '出勤中',
        ]);

        return redirect()->route('attendance.index')->with('message', '出勤しました。');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', now()->toDateString())
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance.index')->with('message', '出勤記録が見つかりません。');
        }

        $attendance->update([
            'clock_out_time' => now()->format('H:i:s'),
            'status' => '退勤済',
        ]);

        return redirect()->route('attendance.index')->with('message', '退勤しました。');
    }

    public function breakIn()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance || $attendance->status !== '出勤中') {
            return redirect()->route('attendance.index')->with('message', '休憩は出勤中のみ行えます。');
        }

        $attendance->status = '休憩中';
        $attendance->save();

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now()->format('H:i:s'),
        ]);

        return redirect()->route('attendance.index')->with('message', '休憩を開始しました。');
    }

    public function breakOut()
    {
        $attendanceId = $this->getTodayAttendanceId();

        $break = BreakTime::where('attendance_id', $attendanceId)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if (!$break) {
            return redirect()->route('attendance.index')->with('message', '休憩中の記録が見つかりませんでした。');
        }

        $break->update([
            'break_end' => now()->format('H:i:s'),
        ]);

        $this->updateStatus('出勤中');

        return redirect()->route('attendance.index')->with('message', '休憩終了しました。');
    }

    private function getTodayAttendanceId()
    {
        return Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', now()->toDateString())
            ->value('id');
    }

    private function updateStatus($status)
    {
        Attendance::where('user_id', Auth::id())
            ->whereDate('work_date', now()->toDateString())
            ->update(['status' => $status]);
    }
}
