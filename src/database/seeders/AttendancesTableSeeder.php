<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        $months = [
            Carbon::now()->subMonth(),
            Carbon::now(),
            Carbon::now()->addMonth(),
        ];

        foreach ($users as $user) {
            foreach ($months as $month) {
                $daysInMonth = $month->daysInMonth;
                $yearMonth = $month->format('Y-m');

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromFormat('Y-m-d', "$yearMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT));

                    Attendance::factory()->create([
                        'user_id' => $user->id,
                        'work_date' => $date->toDateString(),
                    ]);
                }
            }
        }
    }
}
