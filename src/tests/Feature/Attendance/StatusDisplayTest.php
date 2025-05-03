<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class StatusDisplayTest extends TestCase
{
  use RefreshDatabase;

  private function createUserWithStatus(?string $status): User
  {
    $user = User::factory()->create([
      'email_verified_at' => Carbon::now(),
    ]);

    // ステータスが null（勤務外）でない場合のみ出勤レコードを登録
    if ($status) {
      Attendance::create([
        'user_id' => $user->id,
        'work_date' => Carbon::today()->toDateString(),
        'clock_in_time' => '09:00:00',
        'status' => $status,
      ]);
    }

    return $user;
  }

  /** @test */
  public function 勤務外の場合_勤怠ステータスが勤務外と表示される()
  {
    $user = $this->createUserWithStatus(null);
    $this->actingAs($user);

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('勤務外');
  }

  /** @test */
  public function 出勤中の場合_勤怠ステータスが出勤と表示される()
  {
    $user = $this->createUserWithStatus('出勤');
    $this->actingAs($user);

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('出勤');
  }

  /** @test */
  public function 休憩中の場合_勤怠ステータスが休憩中と表示される()
  {
    $user = $this->createUserWithStatus('休憩中');
    $this->actingAs($user);

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('休憩中');
  }

  /** @test */
  public function 退勤済の場合_勤怠ステータスが退勤済と表示される()
  {
    $user = $this->createUserWithStatus('退勤済');
    $this->actingAs($user);

    $response = $this->get(route('attendance.index'));

    $response->assertStatus(200);
    $response->assertSee('退勤済');
  }
}
