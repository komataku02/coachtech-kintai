<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function 会員登録後に認証メールが送信される()
  {
    Notification::fake();

    $response = $this->post('/register', [
      'name' => 'テストユーザー',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    Notification::assertSentTo($user, VerifyEmail::class);
  }

  /** @test */
  public function 認証案内画面が表示される()
  {
    $user = User::factory()->create(['email_verified_at' => null]);

    $this->actingAs($user instanceof \Illuminate\Contracts\Auth\Authenticatable ? $user : User::find($user->id))
      ->get('/email/verify')
      ->assertStatus(200)
      ->assertSee('認証はこちらから');
  }

  /** @test */
  public function メール認証完了後に勤怠画面にリダイレクトされる()
  {
    $user = User::factory()->create(['email_verified_at' => null]);

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
      'verification.verify',
      Carbon::now()->addMinutes(60),
      ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user instanceof \Illuminate\Contracts\Auth\Authenticatable ? $user : User::find($user->id))
      ->get($verificationUrl);

    $response->assertRedirect(route('attendance.index'));

    $this->assertNotNull($user->fresh()->email_verified_at);

    Event::assertDispatched(Verified::class);
  }
}
