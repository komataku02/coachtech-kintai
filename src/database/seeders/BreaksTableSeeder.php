<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreaksTableSeeder extends Seeder
{
    public function run(): void
    {
        $attendances = Attendance::whereNotNull('clock_in_time')
            ->whereNotNull('clock_out_time')
            ->get();

        foreach ($attendances as $attendance) {
            // 出勤〜退勤間の時間をCarbonで扱う
            $clockIn = Carbon::parse($attendance->clock_in_time);
            $clockOut = Carbon::parse($attendance->clock_out_time);

            // 休憩の回数（1〜3回のランダム）
            $breakCount = rand(1, 3);

            for ($i = 0; $i < $breakCount; $i++) {
                // 出勤時間 + 1〜3時間の範囲で休憩開始
                $breakStart = $clockIn->copy()->addHours(rand(1, 3))->addMinutes(rand(0, 59));
                // 休憩終了は開始から15〜60分後
                $breakEnd = $breakStart->copy()->addMinutes(rand(15, 60));

                // 退勤時間を超えてしまう場合はスキップ
                if ($breakEnd->gt($clockOut)) {
                    continue;
                }

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $breakStart->format('H:i:s'),
                    'break_end' => $breakEnd->format('H:i:s'),
                ]);
            }
        }
    }
}
