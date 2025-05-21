<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'attendance_id' => null,
            'status' => 'pending',
            'note' => '出勤打刻を忘れました。',
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_breaks' => json_encode([]),
            'request_at' => now(),
        ];
    }
}
