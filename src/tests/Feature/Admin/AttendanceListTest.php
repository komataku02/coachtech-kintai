<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
  use RefreshDatabase;

  protected $admin;
  protected $users;

  protected function setUp(): void
  {
    parent::setUp();

    // 管理者ユーザー
    $this->admin = User::factory()->create([
      'name' => '管理者太郎',
      'email' => 'admin@example.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
      'email_verified_at' => Carbon::now(),
    ]);

    // 一般ユーザー5人
    $this->users = User::factory()->count(5)->create();

    // 勤怠データ（本日）
    foreach ($this->users as $user) {
      Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => Carbon::today()->toDateString(),
      ]);
    }
  }

  /** @test */
  public function その日になされた全ユーザーの勤怠情報が正確に確認できる()
  {
    $response = $this->actingAs($this->admin)->get(route('admin.attendance.index'));

    $response->assertStatus(200);

    foreach ($this->users as $user) {
      $response->assertSee($user->name);
    }
  }

  /** @test */
  public function 遷移した際に現在の日付が表示される()
  {
    $response = $this->actingAs($this->admin)->get(route('admin.attendance.index'));

    $response->assertStatus(200);
    $response->assertSee(Carbon::today()->format('Y-m-d'));
  }

  /** @test */
  public function 「前日」を押下した時に前の日の勤怠情報が表示される()
  {
    $yesterday = Carbon::yesterday()->toDateString();

    foreach ($this->users as $user) {
      Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => $yesterday,
      ]);
    }

    $response = $this->actingAs($this->admin)->get(route('admin.attendance.index', ['date' => $yesterday]));

    $response->assertStatus(200);
    $response->assertSee($yesterday);
  }

  /** @test */
  public function 「翌日」を押下した時に次の日の勤怠情報が表示される()
  {
    $tomorrow = Carbon::tomorrow()->toDateString();

    foreach ($this->users as $user) {
      Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => $tomorrow,
      ]);
    }

    $response = $this->actingAs($this->admin)->get(route('admin.attendance.index', ['date' => $tomorrow]));

    $response->assertStatus(200);
    $response->assertSee($tomorrow);
  }
}
