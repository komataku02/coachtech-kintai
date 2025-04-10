<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'request_reason' => $this->faker->realText(50),
            'request_at' => now()->subDays(rand(1, 10)),
            'status' => 'pending',
            'approved_at' => null,
        ];
    }
}
