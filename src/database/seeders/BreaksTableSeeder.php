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
            $clockIn = Carbon::createFromFormat('H:i:s', $attendance->clock_in_time);
            $clockOut = Carbon::createFromFormat('H:i:s', $attendance->clock_out_time);

            $breakStart = (clone $clockIn)->addHours(4);
            $breakEnd = (clone $breakStart)->addHour();

            if ($breakEnd->lte($clockOut)) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $breakStart->format('H:i:s'),
                    'break_end' => $breakEnd->format('H:i:s'),
                ]);
            }
        }
    }
}
