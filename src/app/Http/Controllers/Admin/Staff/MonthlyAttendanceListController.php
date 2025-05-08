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

        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth()->startOfDay();
        $endOfMonth = Carbon::parse($month)->endOfMonth()->endOfDay();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')
            ->get();

        return view('admin.staff.attendance', [
            'user' => $user,
            'attendances' => $attendances,
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
                $clockIn ? $clockIn->format('H:i') : '',
                $clockOut ? $clockOut->format('H:i') : '',
                $totalBreak,
                $totalWork,
                $attendance->status,
                $attendance->note ?? '',
            ];
        }

        $filename = "{$month}_{$user->name}_勤怠一覧.csv";
        $filename = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');

        return Response::stream(function () use ($csvData) {
            $stream = fopen('php://output', 'w');
            foreach ($csvData as $row) {
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
