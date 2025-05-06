<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
<<<<<<< HEAD
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\User\AttendanceFormRequest;
=======
use App\Models\Attendance;
use App\Models\Application;
>>>>>>> feature/admin-analytics

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
<<<<<<< HEAD
        $user = Auth::user();
        if ($attendance->user_id !== $user->id) {
            return redirect()->route('attendance.index')->with('error', '他のユーザーの勤怠情報は表示できません。');
=======

        // 自分の勤怠でなければ403
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '他人の勤怠にはアクセスできません。');
>>>>>>> feature/admin-analytics
        }

        // 修正申請済みかどうかをチェック
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
