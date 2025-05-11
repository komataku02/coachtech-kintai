@extends('layouts.app')

@section('page-css')
<link rel="stylesheet" href="{{ asset('css/common/auth.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2 class="form-title">ログイン</h2>

    <form action="{{ route('login') }}" method="POST" class="form-box">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" class="form-input">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group center">
            <button type="submit" class="submit-button">ログイン</button>
        </div>

        <div class="form-group center">
            <a href="{{ route('register') }}" class="link-login">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection
