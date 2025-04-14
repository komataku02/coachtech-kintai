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
        $attendance = Attendance::findOrFail($attendance_id);

        // 自分の勤怠以外には申請できないよう制限
        if ($attendance->user_id !== Auth::id()) {
            abort(403, '他人の勤怠に対しては申請できません。');
        }

        // すでに申請済みか確認
        $alreadyApplied = Application::where('attendance_id', $attendance->id)
            ->where('user_id', Auth::id())
            ->exists();

        return view('application.create', compact('attendance', 'alreadyApplied'));
    }

    /**
     * 申請の登録処理
     */
    public function store(ApplicationFormRequest $request)
    {
        Application::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'request_reason' => $request->request_reason,
            'request_at' => now(),
            'status' => 'pending',
        ]);

        return redirect()->route('application.list')
            ->with('message', '修正申請を送信しました。');
    }
}
