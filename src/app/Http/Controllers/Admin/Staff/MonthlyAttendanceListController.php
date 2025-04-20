<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class MonthlyAttendanceListController extends Controller
{
    public function show(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // クエリから月を取得、なければ今月
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        // 該当月の範囲を定義
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        // 勤怠データを取得（月内）
        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')
            ->get();

        // ステータス集計
        $statusCounts = $attendances->groupBy('status')->map->count();

        return view('admin.staff.attendance', [
            'user' => $user,
            'attendances' => $attendances,
            'statusCounts' => $statusCounts,
            'month' => $month,
        ]);
    }

    public function downloadCsv($userId)
    {
        $user = User::findOrFail($userId);

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->get();

        // CSVヘッダ
        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩時間（分）', '合計勤務時間（分）', 'ステータス', '備考'];

        foreach ($attendances as $attendance) {
            // 出勤・退勤のCarbonインスタンス作成
            $clockIn = $attendance->clock_in_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_in_time) : null;
            $clockOut = $attendance->clock_out_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_out_time) : null;

            // 合計休憩時間（分）
            $totalBreak = $attendance->breakTimes->sum(function ($break) {
                if ($break->break_start && $break->break_end) {
                    $start = Carbon::createFromFormat('H:i:s', $break->break_start);
                    $end = Carbon::createFromFormat('H:i:s', $break->break_end);
                    return $end->diffInMinutes($start);
                }
                return 0;
            });

            // 合計勤務時間（分）＝退勤 - 出勤 - 休憩
            $totalWork = ($clockIn && $clockOut)
                ? $clockOut->diffInMinutes($clockIn) - $totalBreak
                : null;

            $csvData[] = [
                $attendance->work_date,
                $clockIn ? $clockIn->format('H:i') : '',
                $clockOut ? $clockOut->format('H:i') : '',
                $totalBreak,
                $totalWork,
                $attendance->status,
                $attendance->note ?? '',
            ];
        }

        $filename = 'attendance_' . $user->name . '_' . now()->format('Ymd_His') . '.csv';

        return Response::stream(function () use ($csvData) {
            $stream = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
