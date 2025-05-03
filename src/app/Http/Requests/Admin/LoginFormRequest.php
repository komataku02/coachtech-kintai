<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'email' => 'required|email',
      'password' => 'required|string|min:8|max:20',
    ];
  }

  public function messages(): array
  {
    return [
      'email.required' => 'メールアドレスを入力してください',
      'email.email' => '有効なメールアドレス形式で入力してください',
      'password.required' => 'パスワードを入力してください',
    ];
  }
}
