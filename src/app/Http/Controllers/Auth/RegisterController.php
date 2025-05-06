<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\RegisterFormRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterFormRequest $request)
    {
        // ユーザーを作成（email_verified_at は null のままでOK）
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // 認証メール送信イベント
        event(new Registered($user));

        // 自動ログイン
        Auth::login($user);

        // 認証案内画面へリダイレクト
        return redirect()->route('verification.notice');
    }
}
