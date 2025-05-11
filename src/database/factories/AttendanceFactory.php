<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        $today = now()->format('Y-m-d');
        $clockIn = $this->faker->dateTimeBetween("$today 08:00:00", "$today 09:30:00");
        $clockOut = (clone $clockIn)->modify('+9 hours');

        return [
            'clock_in_time' => $clockIn->format('H:i:s'),
            'clock_out_time' => $clockOut->format('H:i:s'),
            'status' => '退勤済',
            'note' => $this->faker->optional()->realText(30),
        ];
    }
}
