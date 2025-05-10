<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\BreakTime;
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
        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::findOrFail($id);

            $attendance->clock_in_time = $request->clock_in_time;
            $attendance->clock_out_time = $request->clock_out_time;
            $attendance->note = $request->note;
            $attendance->save();

            if ($request->has('breaks')) {
                foreach ($request->breaks as $breakId => $times) {
                    $break = BreakTime::find($breakId);
                    if ($break && $break->attendance_id == $attendance->id) {
                        $break->break_start = $times['start'];
                        $break->break_end = $times['end'];
                        $break->save();
                    }
                }
            }
        });

        return redirect()->route('attendance.show', $id)
            ->with('message', '勤怠情報を更新しました。');
    }
}
