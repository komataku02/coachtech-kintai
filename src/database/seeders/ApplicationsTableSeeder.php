<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Application;

class ApplicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
                Application::create([
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'request_reason' => $requestReasons[array_rand($requestReasons)],
                    'request_at' => now()->subDays(rand(1, 10)),
                    'status' => 'pending',
                    'approved_at' => null,
                ]);
            }
        }
    }
}
