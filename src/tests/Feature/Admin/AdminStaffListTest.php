<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminStaffListTest extends TestCase
{
  use RefreshDatabase;

  protected $admin;
  protected $users;

  protected function setUp(): void
  {
    parent::setUp();

    Carbon::setTestNow(Carbon::create(2025, 5, 15));

    $this->admin = User::factory()->create([
      'name' => '管理者太郎',
      'email' => 'admin@example.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
      'email_verified_at' => now(),
    ]);

    $this->users = User::factory()->count(5)->create([
      'email_verified_at' => now(),
      'role' => 'user',
    ]);

    foreach ($this->users as $user) {
      foreach ([-1, 0, 1] as $monthOffset) {
        $date = Carbon::now()->copy()->addMonths($monthOffset);

        Attendance::factory()->create([
          'user_id' => $user->id,
          'work_date' => $date->format('Y-m-d'),
          'clock_in_time' => '09:00:00',
          'clock_out_time' => '18:00:00',
          'note' => '勤務記録',
        ]);
      }
    }
  }


  /** @test */
  public function 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる()
  {
    $response = $this->actingAs($this->admin)
      ->get(route('admin.staff.list'));

    $response->assertStatus(200);

    foreach ($this->users as $user) {
      $response->assertSee($user->name);
      $response->assertSee($user->email);
    }
  }

  /** @test */
  public function ユーザーの勤怠情報が正しく表示される()
  {
    $user = $this->users->first();
    $month = now()->format('Y-m');

    $response = $this->actingAs($this->admin)
      ->get(route('admin.staff.attendance', ['id' => $user->id, 'month' => $month]));

    $response->assertStatus(200);
    $response->assertSee('09:00');
    $response->assertSee('18:00');
    $response->assertSee('日付');
  }

  /** @test */
  public function 「前月」を押下した時に表示月の前月の情報が表示される()
  {
    $user = $this->users->first();
    $month = now()->copy()->subMonth()->format('Y-m');

    $response = $this->actingAs($this->admin)
      ->get(route('admin.staff.attendance', ['id' => $user->id, 'month' => $month]));

    $response->assertStatus(200);
    $response->assertSee('09:00');
    $response->assertSee('18:00');
  }

  /** @test */
  public function 「翌月」を押下した時に表示月の前月の情報が表示される()
  {
    $user = $this->users->first();
    $month = now()->copy()->addMonth()->format('Y-m');

    $response = $this->actingAs($this->admin)
      ->get(route('admin.staff.attendance', ['id' => $user->id, 'month' => $month]));

    $response->assertStatus(200);
    $response->assertSee('09:00');
    $response->assertSee('18:00');
  }

  /** @test */
  public function 「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
  {
    $attendance = Attendance::first();

    $response = $this->actingAs($this->admin)
      ->get(route('admin.attendance.detail', $attendance->id));

    $response->assertStatus(200);
    $response->assertSee('勤怠詳細');
    $response->assertSee($attendance->note);
  }
}
