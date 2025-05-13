<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\ApplicationFormRequest;
use App\Models\Attendance;
use App\Models\Application;

class SubmitController extends Controller
{

    public function store(ApplicationFormRequest $request)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($request->attendance_id);

        if (Application::where('attendance_id', $attendance->id)->where('user_id', Auth::id())->exists()) {
            return redirect()->route('attendance.show', ['id' => $attendance->id])
                ->with('error', 'この勤怠にはすでに申請済みです。');
        }

        $breaks = [];
        $startTimes = $request->input('break_start_times', []);
        $endTimes = $request->input('break_end_times', []);
        foreach ($startTimes as $i => $start) {
            if (!empty($start) && !empty($endTimes[$i])) {
                $breaks[] = [
                    'start' => $start,
                    'end'   => $endTimes[$i],
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

        return redirect()->route('attendance.show', ['id' => $attendance->id])
            ->with('message', '修正申請を送信しました。');
    }
}
