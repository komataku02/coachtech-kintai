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

        foreach ($users as $index => $user) {
            for ($i = 0; $i < 10; $i++) {
                $workDate = Carbon::now()->subDays($index * 20 + $i)->toDateString();

                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'work_date' => $workDate,
                ]);
            }
        }
    }
}
