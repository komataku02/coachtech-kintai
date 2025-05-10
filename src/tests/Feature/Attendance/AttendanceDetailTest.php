<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

class AttendanceDetailTest extends TestCase
{
  use RefreshDatabase;

  protected $user;
  protected $attendance;

  protected function setUp(): void
  {
    parent::setUp();

    $this->withoutMiddleware([
      EnsureEmailIsVerified::class,
    ]);

    Carbon::setTestNow(Carbon::create(2025, 5, 1, 10, 0, 0));

    $this->user = User::factory()->create([
      'email_verified_at' => now(),
    ]);

    $this->actingAs($this->user instanceof Authenticatable ? $this->user : User::find($this->user->id));

    $this->attendance = Attendance::factory()->create([
      'user_id' => $this->user->id,
      'work_date' => '2025-05-01',
      'clock_in_time' => '08:30:00',
      'clock_out_time' => '17:45:00',
    ]);

    BreakTime::create([
      'attendance_id' => $this->attendance->id,
      'break_start' => '12:00:00',
      'break_end' => '13:00:00',
    ]);
  }

  /** @test */
  public function 名前が正しく表示される()
  {
    $response = $this->get(route('attendance.show', $this->attendance->id));
    $response->assertStatus(200);
    $response->assertSee($this->user->name);
  }

  /** @test */
  public function 日付が正しく表示される()
  {
    $response = $this->get(route('attendance.show', $this->attendance->id));
    $response->assertStatus(200);
    $response->assertSee('2025年5月1日');
  }

  /** @test */
  public function 出勤時間が正しく表示される()
  {
    $response = $this->get(route('attendance.show', $this->attendance->id));
    $response->assertStatus(200);
    $response->assertSee('08:30');
  }

  /** @test */
  public function 退勤時間が正しく表示される()
  {
    $response = $this->get(route('attendance.show', $this->attendance->id));
    $response->assertStatus(200);
    $response->assertSee('17:45');
  }

  /** @test */
  public function 休憩時間が正しく表示される()
  {
    $response = $this->get(route('attendance.show', $this->attendance->id));
    $response->assertStatus(200);
    $response->assertSee('12:00');
    $response->assertSee('13:00');
  }
}
