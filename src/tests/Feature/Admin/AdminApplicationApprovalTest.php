<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Application;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminApplicationApprovalTest extends TestCase
{
  use RefreshDatabase;

  protected $admin;
  protected $user;

  protected function setUp(): void
  {
    parent::setUp();

    // Seederを使わず、必要なユーザーのみをFactoryで用意
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->user = User::factory()->create(['role' => 'user']);
  }

  /** @test */
  public function 承認待ちの修正申請が全て表示される()
  {
    $attendance = Attendance::factory()->create([
      'user_id' => $this->user->id,
      'work_date' => now()->subDays(1)->format('Y-m-d'),
    ]);

    Application::factory()->create([
      'user_id' => $this->user->id,
      'attendance_id' => $attendance->id,
      'status' => 'pending',
      'note' => '出勤打刻を忘れました。',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.application.list', ['status' => 'pending']));

    $response->assertStatus(200);
    $response->assertSee('承認待ち');
    $response->assertSeeText($this->user->name);
    $response->assertSeeText('出勤打刻を忘れました。');
  }

  /** @test */
  public function 承認済みの修正申請が全て表示される()
  {
    $attendance = Attendance::factory()->create([
      'user_id' => $this->user->id,
    ]);

    $application = Application::factory()->create([
      'user_id' => $this->user->id,
      'attendance_id' => $attendance->id,
      'status' => 'approved',
      'approved_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.application.list', ['status' => 'approved']));

    $response->assertStatus(200);
    $response->assertSee('承認済み');
    $response->assertSeeText($this->user->name);
    $response->assertSeeText($application->note);
  }

  /** @test */
  public function 修正申請の詳細内容が正しく表示される()
  {
    $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

    $application = Application::factory()->create([
      'user_id' => $this->user->id,
      'attendance_id' => $attendance->id,
      'status' => 'pending',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.application.detail', $application->id));

    $response->assertStatus(200);
    $response->assertSeeText($this->user->name);
    $response->assertSeeText($application->note);

    $workDate = \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日');
    $response->assertSeeText($workDate);
  }

  /** @test */
  public function 修正申請の承認処理が正しく行われる()
  {
    $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

    $application = Application::factory()->create([
      'user_id' => $this->user->id,
      'attendance_id' => $attendance->id,
      'status' => 'pending',
      'request_clock_in' => '09:00',
      'request_clock_out' => '18:00',
      'request_breaks' => json_encode([
        ['start' => '12:00', 'end' => '13:00']
      ]),
    ]);

    $response = $this->actingAs($this->admin)->post(route('admin.application.approve', $application->id));

    $response->assertRedirect(route('admin.application.detail', $application->id));

    $this->assertDatabaseHas('applications', [
      'id' => $application->id,
      'status' => 'approved',
    ]);

    $this->assertDatabaseHas('attendances', [
      'id' => $attendance->id,
      'clock_in_time' => '09:00',
      'clock_out_time' => '18:00',
    ]);

    $this->assertDatabaseHas('breaks', [
      'attendance_id' => $attendance->id,
      'break_start' => '12:00',
      'break_end' => '13:00',
    ]);
  }
}
