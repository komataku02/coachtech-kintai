<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\User\AttendanceFormRequest;

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $user = Auth::user();
        if ($attendance->user_id !== $user->id) {
            return redirect()->route('attendance.index')->with('error', '他のユーザーの勤怠情報は表示できません。');
        }
        return view('attendance.show', compact('attendance'));
    }

    public function update(AttendanceFormRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::findOrFail($id);

            $attendance->clock_in_time = $request->clock_in_time;
            $attendance->clock_out_time = $request->clock_out_time;
            $attendance->note = $request->note;
            $attendance->save();

            // 休憩時間の更新
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

        return redirect()->route('attendance.show', $id)->with('message', '勤怠情報を更新しました。');
    }
}
