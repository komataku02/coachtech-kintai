<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\ApplicationFormRequest;
use App\Models\Attendance;
use App\Models\Application;

class SubmitController extends Controller
{
    /**
     * 申請フォームの表示
     */
    public function create($attendance_id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($attendance_id);

        if ($attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.index')->with('error', '他のユーザーの勤怠には申請できません。');
        }

        $alreadyApplied = Application::where('attendance_id', $attendance->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('application.create', compact('attendance', 'alreadyApplied'));
    }

    /**
     * 修正申請の登録処理
     */
    public function store(ApplicationFormRequest $request)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($request->attendance_id);

        // 休憩時間を配列で保存（JSONとして格納）
        $breaks = [];
        foreach ($attendance->breakTimes as $break) {
            $id = $break->id;
            if (isset($request->breaks[$id]['start'], $request->breaks[$id]['end'])) {
                $breaks[] = [
                    'start' => $request->breaks[$id]['start'],
                    'end'   => $request->breaks[$id]['end'],
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

        return redirect()->route('application.list')
            ->with('message', '修正申請を送信しました。');
    }
}
