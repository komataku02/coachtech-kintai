<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

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
}
