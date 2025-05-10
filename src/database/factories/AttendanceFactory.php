<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        //勤怠ステータスをランダム生成
        $status = Arr::random(['出勤','休憩中','退勤済','勤務外']);
        $today = now()->format('Y-m-d');
        $clockIn = $this->faker->dateTimeBetween("$today 08:00:00","$today 10:00:00");
        $clockOut = $this->faker->dateTimeBetween($clockIn->format('Y-m-d H:i:s'),"$today 20:00:00");

        return [
            //'user_id' => 1,
            'clock_in_time' => $clockIn->format('H:i:s'),
            'clock_out_time' => $clockOut->format('H:i:s'),
            'status' => $status,
            'note' => $this->faker->optional()->realText(30),
        ];
    }
}
