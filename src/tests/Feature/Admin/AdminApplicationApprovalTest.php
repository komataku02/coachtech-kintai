<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
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

    // Seeder実行（Users, Attendances, Applications）
    $this->artisan('db:seed');

    $this->admin = User::where('role', 'admin')->first();
    $this->user = User::where('role', 'user')->first();
  }

  /** @test */
  public function 承認待ちの修正申請が全て表示される()
  {
    $response = $this->actingAs($this->admin instanceof Authenticatable ? $this->admin : User::find($this->admin->id))
      ->get(route('admin.application.list', ['status' => 'pending']));

    $response->assertStatus(200);
    $response->assertSee('承認待ち');

    $application = Application::where('status', 'pending')->first();
    $response->assertSeeText($application->user->name);
    $response->assertSeeText($application->note);
    }

  /** @test */
  public function 承認済みの修正申請が全て表示される()
  {
    Application::where('status', 'pending')->first()->update([
      'status' => 'approved',
      'approved_at' => now(),
    ]);

    $response = $this->actingAs($this->admin instanceof Authenticatable ? $this->admin : User::find($this->admin->id))
      ->get(route('admin.application.list', ['status' => 'approved']));

    $response->assertStatus(200);
    $response->assertSee('承認済み');

    $approvedApps = Application::where('status', 'approved')->get();
    foreach ($approvedApps as $app) {
      $response->assertSee($app->user->name);
      $response->assertSee($app->note);
    }
  }

  /** @test */
  public function 修正申請の詳細内容が正しく表示される()
  {
    $application = Application::with('user', 'attendance.breakTimes')
      ->where('status', 'pending')
      ->firstOrFail();

    $response = $this->actingAs($this->admin instanceof Authenticatable ? $this->admin : User::find($this->admin->id))
      ->get(route('admin.application.detail', $application->id));

    $response->assertStatus(200);
    $response->assertSeeText($application->user->name);
    $response->assertSeeText($application->note);

    $workDate = \Carbon\Carbon::parse($application->attendance->work_date)->format('Y年n月j日');
    $response->assertSeeText($workDate);
  }


  /** @test */
  public function 修正申請の承認処理が正しく行われる()
  {
    $application = Application::where('status', 'pending')->first();
    $attendance = $application->attendance;

    $response = $this->actingAs($this->admin instanceof Authenticatable ? $this->admin : User::find($this->admin->id))
      ->post(route('admin.application.approve', $application->id));

    $response->assertRedirect(route('admin.application.list'));

    $this->assertDatabaseHas('applications', [
      'id' => $application->id,
      'status' => 'approved',
    ]);

    if ($application->request_clock_in) {
      $this->assertDatabaseHas('attendances', [
        'id' => $attendance->id,
        'clock_in_time' => $application->request_clock_in,
      ]);
    }

    if ($application->request_breaks) {
      $breaks = json_decode($application->request_breaks, true);
      foreach ($breaks as $break) {
        $this->assertDatabaseHas('breaks', [
          'attendance_id' => $attendance->id,
          'break_start' => $break['start'],
          'break_end' => $break['end'],
        ]);
      }
    }
  }
}
