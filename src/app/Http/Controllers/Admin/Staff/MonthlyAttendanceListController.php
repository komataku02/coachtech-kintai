<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function downloadCsv(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')
            ->get();

        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩時間（分）', '合計勤務時間（分）', 'ステータス', '備考'];

        foreach ($attendances as $attendance) {
            $clockIn = $attendance->clock_in_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_in_time) : null;
            $clockOut = $attendance->clock_out_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_out_time) : null;

            $totalBreak = $attendance->breakTimes->sum(function ($break) {
                if ($break->break_start && $break->break_end) {
                    $start = Carbon::createFromFormat('H:i:s', $break->break_start);
                    $end = Carbon::createFromFormat('H:i:s', $break->break_end);
                    return $end->diffInMinutes($start);
                }
                return 0;
            });

            $totalWork = ($clockIn && $clockOut)
                ? $clockOut->diffInMinutes($clockIn) - $totalBreak
                : null;

            $csvData[] = [
                $attendance->work_date,
                $clockIn?->format('H:i') ?? '',
                $clockOut?->format('H:i') ?? '',
                $totalBreak,
                $totalWork,
                $attendance->status,
                $attendance->note ?? '',
            ];
        }

        $filename = "{$month}_{$user->name}_勤怠一覧.csv";
        // Windows環境向け：ファイル名の文字コードを Shift-JIS に変換
        $filename = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');

        return Response::stream(function () use ($csvData) {
            $stream = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                // 文字列を Shift-JIS に変換して書き込み
                $convertedRow = array_map(function ($field) {
                    return mb_convert_encoding($field, 'SJIS-win', 'UTF-8');
                }, $row);
                fputcsv($stream, $convertedRow);
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv; charset=Shift_JIS',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
