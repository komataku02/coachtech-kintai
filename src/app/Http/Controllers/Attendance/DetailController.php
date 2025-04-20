<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        // 自分の勤怠データ以外は見れないよう制限
        if ($attendance->user_id !== Auth::id()) {
            abort(403, 'この勤怠情報にアクセスする権限がありません。');
        }
        return view('attendance.show', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'clock_in_time' => ['nullable', 'date_format:H:i'],
            'clock_out_time' => ['nullable', 'date_format:H:i', 'after_or_equal:clock_in_time'],
            'note' => ['nullable', 'string'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end' => ['nullable', 'date_format:H:i'],
        ], [
            'clock_out_time.after_or_equal' => '退勤時刻は出勤時刻より後である必要があります。',
            'breaks.*.start.date_format' => '休憩開始時刻の形式が不正です。',
            'breaks.*.end.date_format' => '休憩終了時刻の形式が不正です。',
        ]);

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

        return redirect()->route('admin.attendance.detail', $id)->with('message', '勤怠情報を更新しました。');
    }
}
