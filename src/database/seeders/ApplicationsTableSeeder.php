<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Application;
use Carbon\Carbon;

class ApplicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        $requestReasons = [
            '退勤時間が早すぎます。',
            '休憩時間を忘れました。',
            '出勤打刻を忘れました。',
            '退勤打刻を忘れました。',
            '出勤時間を間違えました。',
            '休憩時間の記録が間違っています。',
        ];

        foreach ($users as $user) {
            $attendances = Attendance::where('user_id', $user->id)->inRandomOrder()->take(5)->get();

            foreach ($attendances as $attendance) {
                $clockIn = $attendance->clock_in_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_in_time) : null;
                $clockOut = $attendance->clock_out_time ? Carbon::createFromFormat('H:i:s', $attendance->clock_out_time) : null;

                if (!$clockIn || !$clockOut || $clockOut->lte($clockIn)) {
                    continue;
                }

                // 利用可能なスロット（30分単位、1分間隔あり）
                $availableSlots = [];
                $slotStart = $clockIn->copy()->addMinutes(30); // 開始30分後から
                while ($slotStart->addMinutes(30)->lte($clockOut)) {
                    $end = $slotStart->copy()->addMinutes(30);
                    $availableSlots[] = [
                        'start' => $slotStart->copy()->format('H:i'),
                        'end' => $end->format('H:i'),
                    ];
                    $slotStart = $end->copy()->addMinutes(1); // 最低1分空ける
                }

                // スロットからランダムに1〜3件を選択
                shuffle($availableSlots);
                $breaks = array_slice($availableSlots, 0, rand(1, min(3, count($availableSlots))));

                Application::create([
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'request_reason' => $requestReasons[array_rand($requestReasons)],
                    'request_clock_in' => $attendance->clock_in_time,
                    'request_clock_out' => $attendance->clock_out_time,
                    'note' => $attendance->note,
                    'request_breaks' => json_encode($breaks),
                    'request_at' => now()->subDays(rand(1, 10)),
                    'status' => 'pending',
                    'approved_at' => null,
                ]);
            }
        }
    }
}
