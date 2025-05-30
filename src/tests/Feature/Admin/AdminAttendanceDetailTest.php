<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
  use RefreshDatabase;

  protected $admin;
  protected $user;
  protected $attendance;

  protected function setUp(): void
  {
    parent::setUp();

    $this->admin = User::factory()->create([
      'role' => 'admin',
      'email' => 'admin@example.com',
      'password' => Hash::make('password123'),
      'email_verified_at' => now(),
    ]);

    $this->user = User::factory()->create([
      'role' => 'user',
      'email_verified_at' => now(),
    ]);

    $this->attendance = Attendance::factory()->create([
      'user_id' => $this->user->id,
      'work_date' => Carbon::today()->toDateString(),
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'note' => '通常勤務',
    ]);
  }

  /** @test */
  public function 勤怠詳細画面に表示されるデータが選択したものになっている()
  {
    $response = $this->actingAs($this->admin)->get(route('attendance.show', $this->attendance->id));

    $response->assertStatus(200);
    $response->assertSee('勤怠詳細');
    $response->assertSee($this->user->name);
    $response->assertSee('09:00');
    $response->assertSee('18:00');
    $response->assertSee('通常勤務');
  }

  /** @test */
  public function 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
  {
    $response = $this->actingAs($this->admin)->put(route('admin.attendance.update', $this->attendance->id), [
      'clock_in_time' => '19:00',
      'clock_out_time' => '18:00',
      'note' => 'テスト',
    ]);

    $response->assertSessionHasErrors(['clock_out_time']);
  }

  /** @test */
  public function 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
  {
    $response = $this->actingAs($this->admin)->put(route('admin.attendance.update', $this->attendance->id), [
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'break_start_times' => ['19:00'],
      'break_end_times' => ['19:30'],
      'note' => 'テスト',
    ]);

    $response->assertSessionHasErrors(['break_range_error']);
  }

  /** @test */
  public function 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
  {
    $response = $this->actingAs($this->admin)->put(route('admin.attendance.update', $this->attendance->id), [
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'break_start_times' => ['17:00'],
      'break_end_times' => ['19:00'],
      'note' => 'テスト',
    ]);

    $response->assertSessionHasErrors(['break_range_error']);
  }

  /** @test */
  public function 備考欄が未入力の場合のエラーメッセージが表示される()
  {
    $response = $this->actingAs($this->admin)->put(route('admin.attendance.update', $this->attendance->id), [
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'note' => '',
    ]);

    $response->assertSessionHasErrors(['note']);
  }
}
