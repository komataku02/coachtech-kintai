<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;;
use App\Models\Application;
use App\Http\Requests\User\AttendanceFormRequest;

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        if ($attendance->user_id != Auth::id()) {
            return redirect()->route('attendance.list')->with('error', '他人の勤怠にはアクセスできません。');
        }

        $alreadyApplied = Application::where('user_id', Auth::id())
            ->where('attendance_id', $attendance->id)
            ->exists();

        return view('attendance.show', [
            'attendance' => $attendance,
            'alreadyApplied' => $alreadyApplied,
        ]);
    }

    public function update(AttendanceFormRequest $request, $id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        if ($attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.index')->with('error', '他ユーザーの勤怠は修正できません。');
        }

        $alreadyApplied = Application::where('attendance_id', $attendance->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('attendance.show', $id)->with('error', 'すでに修正申請済みです。');
        }

        $breaks = [];
        foreach ($request->input('break_start_times', []) as $index => $start) {
            $end = $request->input('break_end_times.' . $index);
            if ($start && $end) {
                $breaks[] = [
                    'start' => $start,
                    'end' => $end,
                ];
            }
        }

        Application::create([
            'user_id'           => Auth::id(),
            'attendance_id'     => $attendance->id,
            'request_clock_in'  => $request->clock_in_time,
            'request_clock_out' => $request->clock_out_time,
            'note'              => $request->note,
            'request_breaks'    => json_encode($breaks),
            'request_at'        => now(),
            'status'            => 'pending',
        ]);

        return redirect()->route('attendance.show', $attendance->id)
            ->with('message', '修正申請を送信しました。');
    }
}
