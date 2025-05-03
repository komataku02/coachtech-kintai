<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => 'required|string|min:8|confirmed',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'お名前を入力してください',
      'email.required' => 'メールアドレスを入力してください',
      'email.email' => '有効なメールアドレス形式で入力してください',
      'email.unique' => '既に登録されているメールアドレスです',
      'password.required' => 'パスワードを入力してください',
      'password.min' => 'パスワードは8文字以上で入力してください',
      'password.confirmed' => 'パスワードと一致しません',
    ];
  }
}
