<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\AttendanceFormRequest;

class DetailController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with(['breakTimes', 'user'])->findOrFail($id);

        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(AttendanceFormRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::findOrFail($id);

            $attendance->clock_in_time = $request->clock_in_time;
            $attendance->clock_out_time = $request->clock_out_time;
            $attendance->note = $request->note;
            $attendance->save();

            $attendance->breakTimes()->delete();

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
