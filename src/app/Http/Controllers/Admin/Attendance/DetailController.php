<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'clock_in_time' => 'nullable|date_format:H:i',
            'clock_out_time' => 'nullable|date_format:H:i|after_or_equal:clock_in_time',
            'break_start_times.*' => 'nullable|date_format:H:i',
            'break_end_times.*' => 'nullable|date_format:H:i',
            'note' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::findOrFail($id);
            $attendance->clock_in_time = $request->clock_in_time;
            $attendance->clock_out_time = $request->clock_out_time;
            $attendance->note = $request->note;
            $attendance->save();

            // 古い休憩データ削除
            $attendance->breakTimes()->delete();

            // 新しい休憩データ登録（複数対応）
            $starts = $request->input('break_start_times', []);
            $ends = $request->input('break_end_times', []);

            foreach ($starts as $i => $start) {
                if ($start && isset($ends[$i]) && $ends[$i]) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $start,
                        'break_end' => $ends[$i],
                    ]);
                }
            }
        });

        return redirect()->route('admin.attendance.detail', $id)
            ->with('message', '勤怠情報を更新しました');
    }
}
