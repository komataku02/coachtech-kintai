<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\LoginFormRequest;

class LoginController extends Controller
{
    // ログインフォーム表示
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // ログイン処理
    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // 管理者専用ログイン（role: admin）
        if (Auth::attempt(array_merge($credentials, ['role' => 'admin']))) {
            return redirect()->route('admin.application.list')->with('message', 'ログインしました');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません',
        ])->withInput();
    }
}
