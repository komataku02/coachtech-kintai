<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class CurrentTimeTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function 現在の日時が打刻画面に正しく表示されている()
  {
    // ユーザー作成してログイン
    $user = User::factory()->create([
      'email_verified_at' => \Carbon\Carbon::now(),
    ]);
    $this->actingAs($user instanceof User ? $user : User::find($user->id));

    // テスト固定時間を設定（安定性向上のため）
    $fixedNow = Carbon::create(2025, 5, 3, 22, 32);
    Carbon::setTestNow($fixedNow); // ★これで now() が常にこの日時に

    // 日付（年月日 + 曜日）と時刻（H:i）をそれぞれ取得
    $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
    $expectedDate = $fixedNow->format('Y年n月j日') . '（' . $weekDays[$fixedNow->dayOfWeek] . '）';
    $expectedTime = $fixedNow->format('H:i');

    // 勤怠打刻画面へアクセス
    $response = $this->get(route('attendance.index'));

    // ステータスOKかつ、現在日時が表示されているか確認
    $response->assertStatus(200);
    $response->assertSee($expectedDate);
    $response->assertSee($expectedTime);
  }
}
