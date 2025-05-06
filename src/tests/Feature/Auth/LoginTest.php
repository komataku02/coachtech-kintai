<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function メールアドレスが未入力の場合_バリデーションメッセージが表示される()
  {
    $response = $this->from('/login')->post('/login', [
      'email' => '',
      'password' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email']);
  }

  /** @test */
  public function パスワードが未入力の場合_バリデーションメッセージが表示される()
  {
    $response = $this->from('/login')->post('/login', [
      'email' => 'test@example.com',
      'password' => '',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['password']);
  }

  /** @test */
  public function 登録内容と一致しない場合_バリデーションメッセージが表示される()
  {
    User::factory()->create([
      'email' => 'admin@example.com',
      'password' => Hash::make('correct-password'),
    ]);

    $response = $this->post('/admin/login', [
      'email' => 'admin@example.com',
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors([
      'email' => 'ログイン情報が登録されていません',
    ]);
  }
}
