<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;


class BreakTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    Carbon::setTestNow(Carbon::create(2025, 5, 3, 10, 0, 0));
  }

  /** @test */
  public function 出勤中のユーザーに休憩入ボタンが表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'status' => '出勤',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('休憩入');
  }

  /** @test */
  public function 休憩入後ステータスが休憩中になる()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'status' => '出勤',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $this->post(route('attendance.breakIn'));

    $this->assertDatabaseHas('attendances', [
      'user_id' => $user->id,
      'status' => '休憩中',
    ]);
  }

  /** @test */
  public function 休憩戻後ステータスが出勤に戻る()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $attendance = Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'status' => '休憩中',
    ]);

    $attendance->breakTimes()->create([
      'break_start' => now()->subMinutes(30)->format('H:i:s'),
      'break_end' => null,
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $this->post(route('attendance.breakOut'));

    $attendance->refresh();
    $this->assertEquals('出勤', $attendance->status);
  }

  /** @test */
  public function 複数回の休憩が可能である()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $attendance = Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'status' => '出勤',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $this->post(route('attendance.breakIn'));
    $this->post(route('attendance.breakOut'));
    $this->post(route('attendance.breakIn'));

    $this->assertDatabaseHas('attendances', [
      'id' => $attendance->id,
      'status' => '休憩中',
    ]);
  }

  /** @test */
  public function 勤怠一覧画面に休憩時間が表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $attendance = Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'clock_out_time' => '17:00:00',
      'status' => '退勤済',
    ]);
    $attendance->breakTimes()->create([
      'break_start' => Carbon::parse('10:00'),
      'break_end' => Carbon::parse('10:30'),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $response = $this->get(route('attendance.list'));

    $response->assertStatus(200);
    $response->assertSee('0:30');
  }
}
