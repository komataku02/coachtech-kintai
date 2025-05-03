<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // 管理者
        User::create([
            'name' => '管理者太郎',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
        ]);

        // 一般ユーザー
        User::factory()->count(5)->create();
    }
}
