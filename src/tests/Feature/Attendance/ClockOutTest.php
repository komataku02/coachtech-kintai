<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    Carbon::setTestNow(Carbon::create(2025, 5, 3, 18, 0, 0));
  }

  /** @test */
  public function 勤務中のユーザーに退勤ボタンが表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '09:00:00',
      'status' => '出勤',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('退勤');
  }

  /** @test */
  public function 退勤処理後ステータスが退勤済になる()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '09:00:00',
      'status' => '出勤',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $this->post(route('attendance.clockOut'));

    $this->assertDatabaseHas('attendances', [
      'user_id' => $user->id,
      'status' => '退勤済',
    ]);
  }

  /** @test */
  public function 管理画面に退勤時刻が正確に記録されている()
  {
    $admin = User::factory()->create([
      'role' => 'admin',
      'email_verified_at' => now()
    ]);
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:30:00',
      'clock_out_time' => '18:00:00',
      'status' => '退勤済',
    ]);

    $this->actingAs($admin instanceof Authenticatable ? $admin : User::find($admin->id));
    $response = $this->get(route('admin.attendance.index', ['date' => now()->toDateString()]));

    $response->assertStatus(200);
    $response->assertSee('18:00');
  }
}
