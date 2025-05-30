<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules(): array
  {
    return [
      'email' => 'required|email|max:255',
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
