<?php

namespace App\Http\Controllers\Admin\Application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationDetailController extends Controller
{
    public function show($id)
    {
        $application = Application::with(['user', 'attendance.breakTimes'])->findOrFail($id);

        $clockIn = $application->attendance->clock_in_time
            ? \Carbon\Carbon::parse($application->attendance->clock_in_time)->format('H:i')
            : '--:--';

        $clockOut = $application->attendance->clock_out_time
            ? \Carbon\Carbon::parse($application->attendance->clock_out_time)->format('H:i')
            : '--:--';

        return view('admin.application.show', compact('application', 'clockIn', 'clockOut'));
    }

    public function approve(Request $request, $id)
    {
        $application = Application::with('attendance')->findOrFail($id);

        // すでに承認済みの場合はリダイレクト
        if ($application->status === 'approved') {
            return redirect()->route('admin.application.detail', $id)
                ->with('message', 'この申請はすでに承認されています。');
        }

        // 該当の勤怠レコードを取得して申請内容で上書き
        $attendance = $application->attendance;
        $attendance->update([
            'clock_in_time' => $application->request_clock_in,
            'clock_out_time' => $application->request_clock_out,
            'note' => $application->request_note,
        ]);

        // 申請のステータス更新
        $application->status = 'approved';
        $application->approved_at = now();
        $application->save();

        return redirect()->route('admin.application.list')
            ->with('message', '申請を承認し、勤怠情報を更新しました。');
    }
}
