<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Response;

class MonthlyAttendanceListController extends Controller
{
    public function show($userId)
    {
        $user = User::findOrFail($userId);

        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->get();

        // ステータスごとの件数集計
        $statusCounts = $attendances->groupBy('status')->map->count();

        return view('admin.staff.attendance', compact('user', 'attendances', 'statusCounts'));
    }

    public function downloadCsv($userId)
    {
        $user = User::findOrFail($userId);

        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('work_date', 'desc')
            ->get();

        // CSV内容の準備
        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', 'ステータス', '備考'];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->work_date,
                $attendance->clock_in_time ?? '',
                $attendance->clock_out_time ?? '',
                $attendance->status,
                $attendance->note ?? '',
            ];
        }

        $filename = 'attendance_' . $user->name . '_' . now()->format('Ymd_His') . '.csv';

        $response = Response::stream(function () use ($csvData) {
            $stream = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);

        return $response;
    }
}
