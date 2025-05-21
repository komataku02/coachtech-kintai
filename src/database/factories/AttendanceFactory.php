<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        $userId = $this->faker->numberBetween(1, 1000);
        $workDate = now()->subDays($userId)->format('Y-m-d');
        $clockIn = $this->faker->dateTimeBetween("$workDate 08:00:00", "$workDate 09:30:00");
        $clockOut = (clone $clockIn)->modify('+9 hours');

        return [
            'user_id' => null,
            'work_date' => $workDate,
            'clock_in_time' => $clockIn->format('H:i:s'),
            'clock_out_time' => $clockOut->format('H:i:s'),
            'status' => '退勤済',
            'note' => $this->faker->optional()->realText(30),
        ];
    }
}
