<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            //ログインユーザー取得
            $user = Auth::user();

            //ロールに応じてリダイレクト先を分ける
            if ($user->role === 'admin') {
                return redirect()->route('admin.attendance.index');
            }

            return redirect()->route('attendance.index');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }
}
