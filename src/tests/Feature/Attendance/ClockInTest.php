<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class ClockInTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function 勤務外のユーザーに出勤ボタンが表示される()
  {
    $user = User::factory()->create([
      'email_verified_at' => now(),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('出勤');
  }

  /** @test */
  public function 出勤ボタンを押すとステータスが出勤中に更新される()
  {
    $user = User::factory()->create([
      'email_verified_at' => now(),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $response = $this->post(route('attendance.clockIn'));

    $response->assertRedirect(route('attendance.index'));
    $this->assertDatabaseHas('attendances', [
      'user_id' => $user->id,
      'status' => '出勤中',
    ]);
  }

  /** @test */
  public function 出勤済みの場合は出勤ボタンが表示されない()
  {
    $user = User::factory()->create([
      'email_verified_at' => now(),
    ]);

    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => Carbon::today()->toDateString(),
      'clock_in_time' => '09:00:00',
      'status' => '退勤済',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertDontSee('出勤');
  }

  /** @test */
  public function 出勤時刻が管理画面で確認できる()
  {
    $user = User::factory()->create([
      'email_verified_at' => now(),
      'role' => 'admin',
    ]);

    $employee = User::factory()->create([
      'email_verified_at' => now(),
      'role' => 'user',
    ]);

    Attendance::factory()->create([
      'user_id' => $employee->id,
      'work_date' => Carbon::today()->toDateString(),
      'clock_in_time' => '08:45:00',
      'status' => '出勤中',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $response = $this->get(route('admin.attendance.list'));

    $response->assertStatus(200);
    $response->assertSee('08:45');
  }
}
