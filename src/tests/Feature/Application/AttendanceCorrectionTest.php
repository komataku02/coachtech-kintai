<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
  use RefreshDatabase;

  protected $user;
  protected $attendance;

  protected function setUp(): void
  {
    parent::setUp();
    Carbon::setTestNow(Carbon::create(2025, 5, 1, 10, 0, 0));

    $this->user = User::factory()->create([
      'email_verified_at' => now(),
    ]);

    $this->attendance = Attendance::factory()->create([
      'user_id' => $this->user->id,
      'work_date' => '2025-05-01',
      'clock_in_time' => '08:30:00',
      'clock_out_time' => '17:45:00',
      'note' => '通常勤務',
    ]);

    BreakTime::create([
      'attendance_id' => $this->attendance->id,
      'break_start' => '12:00:00',
      'break_end' => '13:00:00',
    ]);

    $this->actingAs($this->user instanceof Authenticatable ? $this->user : User::find($this->user->id));
  }

  /** @test */
  public function 出勤時間が退勤時間より後の場合はエラーメッセージが表示される()
  {
    $response = $this->from(route('attendance.show', $this->attendance->id))
      ->post(route('application.store'), [
        'attendance_id' => $this->attendance->id,
        'clock_in_time' => '18:00',
        'clock_out_time' => '17:00',
        'note' => 'テスト備考',
        'break_start_times' => [],
        'break_end_times' => [],
      ]);

    $response->assertRedirect(route('attendance.show', $this->attendance->id));
    $response->assertSessionHasErrors(['clock_out_time']);
  }

  /** @test */
  public function 休憩開始時間が退勤時間より後の場合はエラーメッセージが表示される()
  {
    $response = $this->from(route('attendance.show', $this->attendance->id))
      ->post(route('application.store'), [
        'attendance_id' => $this->attendance->id,
        'clock_in_time' => '08:30',
        'clock_out_time' => '17:00',
        'note' => 'テスト備考',
        'break_start_times' => ['18:00'],
        'break_end_times' => ['18:30'],
      ]);

    $response->assertRedirect(route('attendance.show', $this->attendance->id));

    $errors = session('errors')->getBag('default')->keys();
    $this->assertTrue(collect($errors)->contains(function ($key) {
      return str_starts_with($key, 'break_range_error');
    }));
  }

  /** @test */
  public function 休憩終了時間が退勤時間より後の場合はエラーメッセージが表示される()
  {
    $response = $this->from(route('attendance.show', $this->attendance->id))
      ->post(route('application.store'), [
        'attendance_id' => $this->attendance->id,
        'clock_in_time' => '08:30',
        'clock_out_time' => '17:00',
        'note' => 'テスト備考',
        'break_start_times' => ['16:00'],
        'break_end_times' => ['18:00'],
      ]);

    $response->assertRedirect(route('attendance.show', $this->attendance->id));

    $errors = session('errors')->getBag('default')->keys();
    $this->assertTrue(collect($errors)->contains(function ($key) {
      return str_starts_with($key, 'break_range_error');
    }));
  }

  /** @test */
  public function 備考欄が未入力の場合はエラーメッセージが表示される()
  {
    $response = $this->from(route('attendance.show', $this->attendance->id))
      ->post(route('application.store'), [
        'attendance_id' => $this->attendance->id,
        'clock_in_time' => '08:30',
        'clock_out_time' => '17:00',
        'note' => '',
        'break_start_times' => [],
        'break_end_times' => [],
      ]);

    $response->assertRedirect(route('attendance.show', $this->attendance->id));
    $response->assertSessionHasErrors(['note']);
  }

  /** @test */
  public function 修正申請処理が実行される()
  {
    $response = $this->post(route('application.store'), [
      'attendance_id' => $this->attendance->id,
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'note' => '修正申請テスト',
      'break_start_times' => ['12:30'],
      'break_end_times' => ['13:30'],
    ]);

    $response->assertRedirect(route('attendance.show', $this->attendance->id));

    $this->assertDatabaseHas('applications', [
      'attendance_id' => $this->attendance->id,
      'note' => '修正申請テスト',
      'request_clock_in' => '09:00',
      'request_clock_out' => '18:00',
      'status' => 'pending',
    ]);
  }

  /** @test */
  public function 承認待ちタブに自分の申請が表示される()
  {
    $this->post(route('application.store'), [
      'attendance_id' => $this->attendance->id,
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'note' => '承認待ちテスト',
      'break_start_times' => [],
      'break_end_times' => [],
    ])->assertRedirect(route('attendance.show', $this->attendance->id));

    $response = $this->get(route('application.list', ['status' => 'pending']));
    $response->assertOk();
    $response->assertSee('承認待ちテスト');
  }

  /** @test */
  public function 承認済みに管理者が承認した申請が表示される()
  {
    $this->post(route('application.store'), [
      'attendance_id' => $this->attendance->id,
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
      'note' => '承認済みテスト',
      'break_start_times' => [],
      'break_end_times' => [],
    ]);

    $application = \App\Models\Application::latest()->first();

    $admin = User::factory()->create([
      'role' => 'admin',
      'email_verified_at' => now(),
    ]);
    $this->actingAs($admin instanceof \Illuminate\Contracts\Auth\Authenticatable ? $admin : User::find($admin->id));

    $this->post(route('admin.application.approve', $application->id));

    $response = $this->get(route('admin.application.list', ['status' => 'approved']));
    $response->assertOk();
    $response->assertSee('承認済みテスト');
  }
}
