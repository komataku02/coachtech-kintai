<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Contracts\Auth\Authenticatable;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
    Carbon::setTestNow(Carbon::create(2025, 5, 3, 10, 0, 0));
  }

  /** @test */
  public function 自分の勤怠情報が全て表示されている()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => Carbon::now()->toDateString(),
      'clock_in_time' => '08:00:00',
      'clock_out_time' => '17:00:00',
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $response = $this->get(route('attendance.list'));

    $response->assertStatus(200);
    $response->assertSee(Carbon::now()->format('m/d'));
  }

  /** @test */
  public function 勤怠一覧画面に遷移した際に現在の月が表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $response = $this->get(route('attendance.list'));

    $response->assertStatus(200);
    $response->assertSee(Carbon::now()->format('Y-m'));
  }

  /** @test */
  public function 前月を押下した際に前月の情報が表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $prevMonth = Carbon::now()->subMonth()->format('Y-m');
    $response = $this->get(route('attendance.list', ['month' => $prevMonth]));

    $response->assertStatus(200);
    $response->assertSee($prevMonth);
  }

  /** @test */
  public function 翌月を押下した際に翌月の情報が表示される()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d'),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));

    $nextMonth = Carbon::now()->addMonth()->format('Y-m');
    $response = $this->get(route('attendance.list', ['month' => $nextMonth]));

    $response->assertStatus(200);
    $response->assertSee($nextMonth);
  }

  /** @test */
  public function 詳細ボタンを押すと勤怠詳細画面に遷移する()
  {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $attendance = Attendance::factory()->create([
      'user_id' => $user->id,
      'work_date' => Carbon::now()->format('Y-m-d'),
    ]);

    $this->actingAs($user instanceof Authenticatable ? $user : User::find($user->id));
    $response = $this->get(route('attendance.list'));

    $response->assertStatus(200);
    $response->assertSee(route('attendance.show', $attendance->id));
  }
}
